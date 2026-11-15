<?php
	include '../php/datamodel.php';
	include '../php/tools.php';

    global $db;
	
	$id = $_GET['arg0'];
	
	$person = $db->selectVal("SELECT p.id, p.lastname, p.firstname, p.id, p.born, p.died FROM people p WHERE p.id = ?", array($id));
	
	// Calculate country
	$person['country'] = getNatio($id);
	
	// Get oscars
	$oscars = $db->select("SELECT m.id, m.titleFr, o.year, o.name, mp.oscarDetail AS detail, mp.oscarWon
							FROM movies_people mp
							INNER JOIN movies m
							ON m.id = mp.movieId
							INNER JOIN oscars o
							ON o.id = mp.oscarId
							WHERE mp.personId = ?
							ORDER BY o.year DESC, o.name, m.id DESC",
							array($id));
	$person['oscars'] = array();
	foreach ($oscars as $o) {
		$o['movie'] = array(
			"id" => $o['id'],
			"titleFr" => $o['titleFr']
		);
		$person['oscars'][] = $o;
	}

    echoResult($person);
?>