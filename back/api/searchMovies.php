<?php
	include '../php/datamodel.php';

    global $db;

	/**
	 * Liste d'identifiants numériques "1,2,3" -> [1, 2, 3].
	 * Renvoie null si le paramètre est vide, un tableau vide s'il ne contient
	 * aucun identifiant valide.
	 */
	function idList($raw) {
		if ($raw === null || $raw === '') {
			return null;
		}
		$ids = array();
		foreach (explode(',', $raw) as $v) {
			$v = trim($v);
			if (ctype_digit($v)) {
				$ids[] = (int) $v;
			}
		}
		return array_values(array_unique($ids));
	}

	/** Liste de chaînes "USA,GBR" -> ['USA', 'GBR'] (valeurs passées en paramètres). */
	function stringList($raw) {
		if ($raw === null || $raw === '') {
			return null;
		}
		return array_values(array_unique(array_filter(array_map('trim', explode(',', $raw)), 'strlen')));
	}

	function placeholders($list) {
		return implode(',', array_fill(0, count($list), '?'));
	}

	$titleFr = $_GET['arg0'] ?? '';
	$title = $_GET['arg1'] ?? '';
	// Années sur 4 chiffres, notes décimales : toute autre valeur est ignorée.
	$yearBefore = ctype_digit($_GET['arg2'] ?? '') ? $_GET['arg2'] : '';
	$yearAfter = ctype_digit($_GET['arg3'] ?? '') ? $_GET['arg3'] : '';
	$markBefore = is_numeric($_GET['arg4'] ?? '') ? (float) $_GET['arg4'] : null;
	$markAfter = is_numeric($_GET['arg5'] ?? '') ? (float) $_GET['arg5'] : null;
	$categoriesIds = idList(urldecode($_GET['arg6'] ?? ''));
	$countries = stringList(urldecode($_GET['arg7'] ?? ''));
	$directorsIds = idList(urldecode($_GET['arg8'] ?? ''));
	$actorsIds = idList(urldecode($_GET['arg9'] ?? ''));
	$owned = !empty($_GET['arg10']);
	$cinema = !empty($_GET['arg11']);

	// Un filtre renseigné sans aucune valeur valide ne peut rien trouver.
	foreach (array($categoriesIds, $countries, $directorsIds, $actorsIds) as $filter) {
		if ($filter === array()) {
			echoResult(array());
			exit;
		}
	}

	$req = "SELECT m.id,
					m.titleFr,
					m.title,
					m.year,
					m.length,
					m.owned,
					m.cinema,
					m.mark,
					m.seenDate
					".($actorsIds ? ", GROUP_CONCAT(mp_acts.role ORDER BY mp_acts.personId SEPARATOR ';') AS roles" : "")."
					".($actorsIds ? ", GROUP_CONCAT(CONCAT('_', mp_acts.personId, '_')) AS actors" : "")."
					".($directorsIds ? ", GROUP_CONCAT(CONCAT('_', mp_dirs.personId, '_')) AS directors" : "")."
					".($categoriesIds ? ", GROUP_CONCAT(CONCAT('_', mc.categoryId, '_')) AS categories" : "")."
					".($countries ? ", GROUP_CONCAT(CONCAT('_', mco.country, '_')) AS countries" : "")."
			FROM ";

	/* FROM AND JOINS */
	if ($actorsIds && $directorsIds) {
		$req .= "(movies_people mp_acts, movies_people mp_dirs)
				INNER JOIN movies m
				ON mp_acts.movieId = m.id
				AND mp_dirs.movieId = m.id ";
	} else if ($actorsIds) {
		$req .= "movies_people mp_acts
				INNER JOIN movies m
				ON mp_acts.movieId = m.id ";
	} else if ($directorsIds) {
		$req .= "movies_people mp_dirs
				INNER JOIN movies m
				ON mp_dirs.movieId = m.id ";
	} else {
		$req .= "movies m ";
	}

	if ($categoriesIds) {
		$req .= "INNER JOIN movies_categories mc
				ON mc.movieId = m.id ";
	}

	if ($countries) {
		$req .= "INNER JOIN movies_countries mco
				ON mco.movieId = m.id ";
	}

	/* WHERE : uniquement des paramètres liés, jamais de valeur concaténée. */
	$where = array();
	$params = array();

	if ($titleFr !== '') { $where[] = "m.titleFr LIKE CONCAT('%', ?, '%')"; $params[] = $titleFr; }
	if ($title !== '') { $where[] = "m.title LIKE CONCAT('%', ?, '%')"; $params[] = $title; }
	// m.year est un varchar(4) : la comparaison reste numérique comme avant.
	if ($yearBefore !== '') { $where[] = "CAST(m.year AS UNSIGNED) >= ?"; $params[] = (int) $yearBefore; }
	if ($yearAfter !== '') { $where[] = "CAST(m.year AS UNSIGNED) <= ?"; $params[] = (int) $yearAfter; }
	if ($markBefore !== null) { $where[] = "m.mark >= ?"; $params[] = $markBefore; }
	if ($markAfter !== null) { $where[] = "m.mark <= ?"; $params[] = $markAfter; }
	if ($categoriesIds) { $where[] = "mc.categoryId IN (".placeholders($categoriesIds).")"; $params = array_merge($params, $categoriesIds); }
	if ($countries) { $where[] = "mco.country IN (".placeholders($countries).")"; $params = array_merge($params, $countries); }
	if ($directorsIds) { $where[] = "mp_dirs.personId IN (".placeholders($directorsIds).") AND mp_dirs.director = 1"; $params = array_merge($params, $directorsIds); }
	if ($actorsIds) { $where[] = "mp_acts.personId IN (".placeholders($actorsIds).") AND mp_acts.staff >= 0"; $params = array_merge($params, $actorsIds); }
	if ($owned) { $where[] = "m.owned = 1"; }
	if ($cinema) { $where[] = "m.cinema = 1"; }

	$req .= "WHERE ".($where ? implode(" AND ", $where) : "1")."
			GROUP BY m.id
			ORDER BY m.year, m.id";

	$movies = $db->select($req, $params);

	// Un film doit contenir TOUTES les valeurs demandées (le IN n'en garantit
	// qu'une) : filtre sur les listes concaténées.
	function keepMatchingAll($movies, $column, $values) {
		return array_values(array_filter($movies, function($m) use ($column, $values) {
			foreach ($values as $v) {
				if (strpos($m[$column], '_'.$v.'_') === false) {
					return false;
				}
			}
			return true;
		}));
	}
	if ($actorsIds) $movies = keepMatchingAll($movies, 'actors', $actorsIds);
	if ($directorsIds) $movies = keepMatchingAll($movies, 'directors', $directorsIds);
	if ($categoriesIds) $movies = keepMatchingAll($movies, 'categories', $categoriesIds);
	if ($countries) $movies = keepMatchingAll($movies, 'countries', $countries);

    echoResult($movies);
?>
