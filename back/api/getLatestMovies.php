<?php
	include '../php/datamodel.php';

    global $db;
	
	$count = $_GET['arg0'];
	
	$movies = $db->select("SELECT m.id, m.title, m.titleFr, m.year, m.length, m.owned, m.cinema, m.mark,
				GROUP_CONCAT(DISTINCT CONCAT(c.id, '_', c.name) ORDER BY mc.main DESC, c.name SEPARATOR ';') AS categories,
				GROUP_CONCAT(DISTINCT CONCAT(director.id, '_', director.firstname, '_', director.lastname) ORDER BY director.lastname, director.firstname SEPARATOR ';') AS directors,
				GROUP_CONCAT(DISTINCT CONCAT(mainactor.id, '_', mainactor.firstname, '_', mainactor.lastname, '_', mp2.role) ORDER BY mainactor.lastname, mainactor.firstname SEPARATOR ';') AS mainactors
			FROM movies m
			INNER JOIN (
				movies_categories mc
				INNER JOIN categories c
				ON c.id = mc.categoryId
			)
			ON m.id = mc.movieId
			INNER JOIN (
				movies_people mp1
				INNER JOIN people director
				ON director.id = mp1.personId
				AND mp1.director = 1
			)
			ON mp1.movieId = m.id
			INNER JOIN (
				movies_people mp2
				INNER JOIN people mainactor
				ON mainactor.id = mp2.personId
				AND mp2.staff = 1
			)
			ON mp2.movieId = m.id
			GROUP BY m.id
			ORDER BY id DESC, m.seenDate DESC
			LIMIT $count");

    echoResult($movies);
?>