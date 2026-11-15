<?php
    include './php/datamodel.php';

    global $db;

    // Genres -> jointure
	
	$bands = $db->select("SELECT id, name from categories");
	
	$categories = array();
	
	foreach ($bands as $b) {
		$categories[$b['name']] = $b['id'];
	}
	
	$movies = $db->select("SELECT id, genre from movies");
	
	$newcats = "INSERT INTO movies_categories (movieId, categoryId) VALUES ";
	foreach ($movies as $m) {
		$newcats .= "(".$m['id'].", ".$categories[$m['genre']]."), ";
	}
	echo $newcats;
?>