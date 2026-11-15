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
		// Get directors and actors
		$people = $db->select('(SELECT p.id, p.lastname, p.firstname, 1 AS director, COUNT(DISTINCT mp.movieId) AS cnt, GROUP_CONCAT(mp.movieId)
								FROM people p
								INNER JOIN movies_people mp
								ON p.id = mp.personId
								WHERE mp.movieId IN ('.$s['ids'].')
								AND director = 1
								GROUP BY mp.personId)
								UNION
								(SELECT p.id, p.lastname, p.firstname, 0 AS director, COUNT(DISTINCT mp.movieId) AS cnt, GROUP_CONCAT(mp.movieId)
								FROM people p
								INNER JOIN movies_people mp
								ON p.id = mp.personId
								WHERE mp.movieId IN ('.$s['ids'].')
								AND staff >= 0
								GROUP BY mp.personId
								HAVING '.($s['cnt'] >= 10 ? 'COUNT(mp.movieId) > 6 OR' : '').' (COUNT(mp.movieId) / '.$s['cnt'].'.0) > 0.65)
								ORDER BY director DESC, cnt DESC, lastname ASC, firstname ASC');
								
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