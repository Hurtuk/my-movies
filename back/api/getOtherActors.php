<?php
	include '../php/datamodel.php';

    global $db;
	
	$movieId = $_GET['arg0'];
	
	$knownActors = $db->selectVal("SELECT GROUP_CONCAT(personId SEPARATOR ',') AS known
									FROM (SELECT DISTINCT personId
											FROM movies_people
											WHERE staff >= 0
											GROUP BY personId
											HAVING SUM(staff) > 0 OR COUNT(movieID) >= 10) T");
	$knownActors = $knownActors['known'];

    $people = $db->select("SELECT p.id, p.firstname, p.lastname, mp.role
		FROM movies_people mp
		INNER JOIN people p
		ON p.id = mp.personId
		WHERE mp.personId IN ($knownActors)
		AND mp.staff = 0
		AND movieId = ?
		ORDER BY p.lastname, p.firstname",
		array($movieId));

    echoResult($people);
?>