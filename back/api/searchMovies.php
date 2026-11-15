<?php
	include '../php/datamodel.php';

    global $db;
	
	$titleFr = $_GET['arg0'];
	$title = $_GET['arg1'];
	$yearBefore = $_GET['arg2'];
	$yearAfter = $_GET['arg3'];
	$markBefore = $_GET['arg4'];
	$markAfter = $_GET['arg5'];
	$categoriesIds = urldecode($_GET['arg6']);
	$countries = urldecode($_GET['arg7']);
	$directorsIds = urldecode($_GET['arg8']);
	$actorsIds = urldecode($_GET['arg9']);
	$owned = $_GET['arg10'] ? 1 : 0;
	$cinema = $_GET['arg11'] ? 1 : 0;
	
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
	
	/* WHERE */
	$req .= "WHERE ";
	
	if ($titleFr) $req .= "m.titleFr LIKE '%".$titleFr."%' AND ";
	if ($title) $req .= "m.title LIKE '%".$title."%' AND ";
	if ($yearBefore) $req .= "m.year >= ".$yearBefore." AND ";
	if ($yearAfter) $req .= "m.year <= ".$yearAfter." AND ";
	if ($markBefore) $req .= "m.mark >= ".$markBefore." AND ";
	if ($markAfter) $req .= "m.mark <= ".$markAfter." AND ";
	if ($categoriesIds) $req .= "mc.categoryId IN (".$categoriesIds.") AND ";
	if ($countries) $req .= "mco.country IN ('".str_replace(",", "','", $countries)."') AND ";
	if ($directorsIds) $req .= "mp_dirs.personId IN (".$directorsIds.") AND mp_dirs.director = 1 AND ";
	if ($actorsIds) $req .= "mp_acts.personId IN (".$actorsIds.") AND mp_acts.staff >= 0 AND ";
	if ($owned) $req .= "m.owned = ".$owned." AND ";
	if ($cinema) $req .= "m.cinema = ".$cinema." AND ";
			
	$req .=	"1 GROUP BY m.id
			ORDER BY m.year, m.id";
		
	$movies = $db->select($req);
	
	// Filters
	if ($actorsIds) {
		$acts = explode(",", $actorsIds);
		for ($i = 0; $i < count($movies); $i++) {
			foreach ($acts as $a) {
				if (strpos($movies[$i]['actors'], '_'.$a.'_') === false) {
					array_splice($movies, $i, 1);
					$i--;
					break;
				}
			}
		}
	}
	if ($directorsIds) {
		$acts = explode(",", $directorsIds);
		for ($i = 0; $i < count($movies); $i++) {
			foreach ($acts as $a) {
				if (strpos($movies[$i]['directors'], '_'.$a.'_') === false) {
					array_splice($movies, $i, 1);
					$i--;
					break;
				}
			}
		}
	}
	if ($categoriesIds) {
		$acts = explode(",", $categoriesIds);
		for ($i = 0; $i < count($movies); $i++) {
			foreach ($acts as $a) {
				if (strpos($movies[$i]['categories'], '_'.$a.'_') === false) {
					array_splice($movies, $i, 1);
					$i--;
					break;
				}
			}
		}
	}
	if ($countries) {
		$acts = explode(",", $countries);
		for ($i = 0; $i < count($movies); $i++) {
			foreach ($acts as $a) {
				if (strpos($movies[$i]['countries'], '_'.$a.'_') === false) {
					array_splice($movies, $i, 1);
					$i--;
					break;
				}
			}
		}
	}

    echoResult($movies);
?>