<?php

	include '../../php/datamodel.php';

    global $db;
	
	$lastmovie = getLastMovie();

	$movies = $db->select("SELECT p.id, p.firstname, p.lastname,
								COUNT(DISTINCT mp.movieId) AS cnt,
								GROUP_CONCAT(m.titleFr ORDER BY m.year DESC, mp.id SEPARATOR '') AS movies
							FROM movies_people mp
							INNER JOIN people p
							ON p.id = mp.personId
							INNER JOIN movies m
							ON m.id = mp.movieId
							WHERE mp.staff >= 0
							AND m.cinema = 1
							GROUP BY mp.personId
							HAVING SUM(mp.staff) > 0 AND cnt >= 4
							ORDER BY cnt DESC");
	$results = array();
	foreach ($movies as $p) {
		$results[] = array(
			"person" => array(
				"id" => $p['id'],
				"lastname" => $p['lastname'],
				"firstname" => $p['firstname'],
				"justseen" => in_array($p['id'], $lastmovie['actors'])
			),
			"movies" => explode('', $p['movies'])
		);
	}

    echoResult($results);
?>