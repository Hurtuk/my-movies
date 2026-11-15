<?php
	include '../../php/datamodel.php';

    global $db;
	
	$sagaId = $_GET['arg0'];
	
	$movies = $db->select("SELECT m.id, m.titleFr, m.year, m.cinema, m.owned, GROUP_CONCAT(mp.personId SEPARATOR ';') AS mainactors
								FROM movies m
								INNER JOIN movies_people mp
								ON mp.movieId = m.id
								WHERE mp.staff >= 0
								AND m.sagaId = ?
								GROUP BY m.id
								ORDER BY m.year",
								array($sagaId));

    echoResult($movies);
?>