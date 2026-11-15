<?php
	header("Access-Control-Allow-Origin: *");

	include '../php/datamodel.php';

    global $db;
	
	$type = $_GET['args0'];
	
	switch ($type) {
		case '0': $top = '0'; break;
		case '1': $top = '100'; break;
		case '2': $top = '200'; break;
		case '3': $top = '300'; break;
	}
	
	$movies = $db->select("SELECT m.id, m.titleFr AS title, m.year, m.length, m.owned, m.overview, m.directors, m.actors, m.categories
			FROM red_movies m
			ORDER BY m.id DESC
			LIMIT 100 OFFSET ".$top);
			
	for ($i = 0; $i < count($movies); $i++) {
		$movies[$i]["categories"] = explode(";", $movies[$i]["categories"]);
		$movies[$i]["directors"] = explode(";", $movies[$i]["directors"]);
		$movies[$i]["actors"] = explode(";", $movies[$i]["actors"]);
		$movies[$i]["owned"] = !!$movies[$i]["owned"];
	}

    echoResult($movies);
?>