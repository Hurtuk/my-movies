<?php
	require_once __DIR__."/php_header.php";
	
	global $db;
	
	$req = "SELECT id FROM movies_people WHERE movieId = ? AND staff = 0";
	$args = array($_POST['film']);
	$res = $db->select($req, $args);
	$where = "(-1";
	foreach ($res as $j) {
		if ($_POST['j'.$j['id']]) {
			$where .= ", ".$j['id'];
		}
	}
	$where .= ")";
	$db->update("UPDATE movies_people SET staff = 1 WHERE id IN $where");
	
	// Update redondance
	
	/*$movie = $db->selectVal("SELECT m.id, m.title, m.titleFr, m.year, m.length, m.owned, m.overview, m.mark,
				GROUP_CONCAT(DISTINCT c.name ORDER BY mc.main DESC, c.name SEPARATOR ';') AS categories,
				GROUP_CONCAT(DISTINCT CONCAT(director.firstname, ' ', director.lastname) ORDER BY director.lastname, director.firstname SEPARATOR ';') AS directors,
				GROUP_CONCAT(DISTINCT CONCAT(mainactor.firstname, ' ', mainactor.lastname) ORDER BY mainactor.lastname, mainactor.firstname SEPARATOR ';') AS actors
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
			ORDER BY m.id DESC
			LIMIT 1");
	$db->update("INSERT INTO red_movies(id, title, titleFr, year, categories, directors, actors, overview, mark, length, owned)
				VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
				array($movie["id"], $movie["title"], $movie["titleFr"], $movie["year"], $movie["categories"], $movie["directors"], $movie["actors"], $movie["overview"], $movie["mark"], $movie["length"], $movie["owned"]));*/
?>