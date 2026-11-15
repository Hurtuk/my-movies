<?php
	include '../../php/datamodel.php';

    global $db;
	
	$lastmovie = getLastMovie();
	
	$sagas = $db->select('SELECT s.id,
								s.title,
								COUNT(m.id) AS cnt,
								GROUP_CONCAT(m.id ORDER BY m.year, m.id) AS ids,
								SUM(m.length) AS length,
								AVG(m.mark) AS mark
							FROM sagas s
							INNER JOIN movies m
							ON m.sagaId = s.id
							GROUP BY s.id
							HAVING COUNT(m.id) >= 3
							ORDER BY cnt DESC, s.title ASC');
							
	// Réalisateurs et acteurs récurrents de toutes les sagas en deux requêtes,
	// au lieu de deux requêtes par saga. Mêmes critères qu'avant :
	// - réalisateurs : tous ;
	// - acteurs : présents dans plus de 65 % des films de la saga, ou dans plus
	//   de 6 films si la saga en compte au moins 10.
	$sagaIds = implode(',', array_map(function($s) { return (int) $s['id']; }, $sagas));
	$peopleBySaga = array();
	if ($sagaIds) {
		$rows = $db->select('SELECT m.sagaId, p.id, p.lastname, p.firstname, 1 AS director, COUNT(DISTINCT mp.movieId) AS cnt
								FROM movies m
								INNER JOIN movies_people mp
								ON mp.movieId = m.id
								INNER JOIN people p
								ON p.id = mp.personId
								WHERE m.sagaId IN ('.$sagaIds.')
								AND mp.director = 1
								GROUP BY m.sagaId, mp.personId
								ORDER BY m.sagaId, cnt DESC, p.lastname ASC, p.firstname ASC');
		$rows = array_merge($rows, $db->select('SELECT m.sagaId, p.id, p.lastname, p.firstname, 0 AS director, COUNT(DISTINCT mp.movieId) AS cnt
								FROM movies m
								INNER JOIN (SELECT sagaId, COUNT(*) AS total FROM movies WHERE sagaId IN ('.$sagaIds.') GROUP BY sagaId) st
								ON st.sagaId = m.sagaId
								INNER JOIN movies_people mp
								ON mp.movieId = m.id
								INNER JOIN people p
								ON p.id = mp.personId
								WHERE mp.staff >= 0
								GROUP BY m.sagaId, mp.personId
								HAVING (MAX(st.total) >= 10 AND COUNT(mp.movieId) > 6)
									OR (COUNT(mp.movieId) / MAX(st.total)) > 0.65
								ORDER BY m.sagaId, cnt DESC, p.lastname ASC, p.firstname ASC'));
		foreach ($rows as $row) {
			$peopleBySaga[$row['sagaId']][] = $row;
		}
	}

	$result = array();

	foreach ($sagas as $s) {
		$r = array(
			"id" => $s['id'],
			"title" => $s['title'],
			"mark" => $s['mark'],
			"length" => $s['length'],
			"count" => $s['cnt']
		);
		$movieIds = explode(",", $s['ids']);
		$r['movies'] = array();
		for ($i = 0; $i < count($movieIds); $i++) {
			$r['movies'][$i] = array(
				"id" => $movieIds[$i]
			);
			if ($movieIds[$i] == $lastmovie['id']) {
				$r['justseen'] = true;
			}
		}
		// Directors and actors (fetched above, directors first)
		$people = isset($peopleBySaga[$s['id']]) ? $peopleBySaga[$s['id']] : array();

		$r['directors'] = array();
		$r['actors'] = array();
		foreach ($people as $p) {
			if ($p['director']) {
				$r['directors'][] = array(
					"id" => $p['id'],
					"firstname" => $p['firstname'],
					"lastname" => $p['lastname'],
					"count" => $p['cnt'],
					"justseen" => in_array($p['id'], $lastmovie['directors'])
				);
			} else {
				$r['actors'][] = array(
					"id" => $p['id'],
					"firstname" => $p['firstname'],
					"lastname" => $p['lastname'],
					"count" => $p['cnt'],
					"justseen" => in_array($p['id'], $lastmovie['actors'])
				);
			}
		}
		$result[] = $r;
	}

    echoResult($result);
?>