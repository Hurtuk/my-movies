<?php
	/**
	 * Collaborations entre deux ensembles de personnes (acteurs/acteurs ou
	 * acteurs/réalisateurs), partagé par api/stats/getCollabs.php et
	 * getCollabsDirs.php.
	 *
	 * Reprend exactement les règles de l'ancien calcul (double boucle sur toutes
	 * les paires de personnes), mais en passant par un index film -> personnes :
	 * on ne visite que les paires qui ont au moins un film en commun.
	 *
	 * Règles conservées :
	 * - paires visitées dans l'ordre (i dans $tab1, j dans $tab2), une personne
	 *   n'est jamais associée à elle-même ;
	 * - une paire est ignorée si la paire inverse (j, i) a déjà été visitée ;
	 * - les films communs sont comptés depuis le côté $tab1, doublons compris
	 *   (une personne peut avoir deux lignes pour le même film) ;
	 * - score = nombre de sagas distinctes + nombre de films hors saga, gardé
	 *   s'il vaut au moins $min ;
	 * - tri par score décroissant, ordre de visite en cas d'égalité.
	 *
	 * @param array $tab1 liste ordonnée de array('id' => personne, 'movies' => [array('id', 'saga')])
	 * @param array $tab2 liste ordonnée de array('id' => personne, 'movies' => [...])
	 * @return array liste de array('a' => id de $tab1, 'b' => id de $tab2, 'cnt', 'justseen')
	 */
	function computeCollabs($tab1, $tab2, $lastMovieId, $min = 4) {
		// Position de chaque personne dans chacun des deux ensembles.
		$pos1 = array();
		foreach ($tab1 as $i => $p) { $pos1[$p['id']] = $i; }
		$pos2 = array();
		foreach ($tab2 as $j => $p) { $pos2[$p['id']] = $j; }

		// Film -> personnes de $tab2 qui y figurent (sans doublon).
		$byMovie = array();
		foreach ($tab2 as $j => $p) {
			foreach ($p['movies'] as $m) {
				$byMovie[$m['id']][$j] = true;
			}
		}

		$pairs = array();
		foreach ($tab1 as $i => $p1) {
			$common = array();
			foreach ($p1['movies'] as $m) {
				if (!isset($byMovie[$m['id']])) {
					continue;
				}
				foreach ($byMovie[$m['id']] as $j => $_) {
					$common[$j][] = $m;
				}
			}
			ksort($common);

			foreach ($common as $j => $movies) {
				$idA = $p1['id'];
				$idB = $tab2[$j]['id'];
				if ($idA == $idB) {
					continue;
				}
				// Paire inverse déjà visitée : $idB est plus haut dans $tab1 et
				// $idA figure dans $tab2.
				if (isset($pos1[$idB]) && $pos1[$idB] < $i && isset($pos2[$idA])) {
					continue;
				}

				$sagas = array();
				$empties = 0;
				$justseen = false;
				foreach ($movies as $m) {
					if (!$m['saga']) {
						$empties++;
					} else {
						$sagas[$m['saga']] = true;
					}
					if ($m['id'] == $lastMovieId) {
						$justseen = true;
					}
				}
				$cnt = count($sagas) + $empties;

				if ($cnt >= $min) {
					$pairs[] = array('a' => (string) $idA, 'b' => (string) $idB, 'cnt' => $cnt, 'justseen' => $justseen);
				}
			}
		}

		// Tri stable (PHP >= 8) : l'ordre de visite départage les égalités.
		usort($pairs, function($x, $y) {
			return $y['cnt'] - $x['cnt'];
		});

		return $pairs;
	}

	/**
	 * Regroupe des lignes (personId, movieId, sagaId) triées par personne en
	 * liste ordonnée pour computeCollabs().
	 */
	function groupCollabRows($rows) {
		$tab = array();
		$last = null;
		foreach ($rows as $r) {
			if ($r['personId'] !== $last) {
				$tab[] = array('id' => $r['personId'], 'movies' => array());
				$last = $r['personId'];
			}
			$tab[count($tab) - 1]['movies'][] = array('id' => $r['movieId'], 'saga' => $r['sagaId']);
		}
		return $tab;
	}

	/**
	 * Acteurs ayant au moins $minFirstRoles premiers rôles, avec tous leurs
	 * films en tant qu'acteur (staff 0 ou 1), triés par personne puis par ligne.
	 */
	function getCollabActors($minFirstRoles) {
		global $db;
		return groupCollabRows($db->select('SELECT mp.personId, mp.movieId, m.sagaId
			FROM (
				SELECT p.id
				FROM movies_people mp
				INNER JOIN people p ON p.id = mp.personId
				INNER JOIN movies m ON m.id = mp.movieId
				WHERE mp.staff != -1
				GROUP BY p.id
				HAVING SUM(mp.staff) >= ?
			) q
			INNER JOIN movies_people mp ON mp.personId = q.id AND mp.staff != -1
			INNER JOIN movies m ON m.id = mp.movieId
			ORDER BY mp.personId, mp.id', array($minFirstRoles)));
	}

	/**
	 * Tous les réalisateurs avec leurs films réalisés.
	 */
	function getCollabDirectors() {
		global $db;
		return groupCollabRows($db->select('SELECT mp.personId, mp.movieId, m.sagaId
			FROM movies_people mp
			INNER JOIN people p ON p.id = mp.personId
			INNER JOIN movies m ON m.id = mp.movieId
			WHERE mp.director = 1
			ORDER BY mp.personId, mp.id'));
	}
?>
