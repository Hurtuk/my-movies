<?php
	include '../../php/datamodel.php';

    global $db;
	
	$limit = $_GET['arg0'];
	
	$lastmovie = getLastMovie();
	
	$people = $db->select('SELECT mp.personId AS id, p.lastname, p.firstname, FORMAT(AVG(m.mark), 2) AS mark, GROUP_CONCAT(movieId SEPARATOR ";") AS movies
							FROM movies_people mp
							INNER JOIN movies m
							ON m.id = mp.movieId
							INNER JOIN people p
							ON p.id = mp.personId
							WHERE mp.director = 1
							GROUP BY personId
							HAVING COUNT(mp.movieId) >= 3
							ORDER BY mark DESC, lastname ASC, firstname ASC
							LIMIT '.($limit + 1));
							
	$result = array();
	
	$lastValue = $people[count($people) - 1]['mark'];
	for ($i = 0; $i < count($people) - 1; $i++) {
		if ($people[$i]['mark'] === $lastValue) {
			break;
		}
		$result[] = array(
			"person" => array(
				"id" => $people[$i]['id'],
				"firstname" => $people[$i]['firstname'],
				"lastname" => $people[$i]['lastname'],
				"justseen" => in_array($people[$i]['id'], $lastmovie['directors'])
			),
			"count" => $people[$i]['mark'],
			"movies" => explode(";", $people[$i]['movies'])
		);
	}

    echoResult($result);
?>