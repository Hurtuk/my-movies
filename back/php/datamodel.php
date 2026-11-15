<?php
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Credentials: true");
	header("Access-Control-Max-Age: 1000");
	header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
	header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");
	header('Content-Type: text/html; charset=utf-8');

	require_once "DB.class.php";
	
	global $db;
	
	$db = new DB();
	
	function getLastMovie() {
		global $db;
		
		$movie = array(
			"actors" => array(),
			"directors" => array(),
			"others" => array(),
			"countries" => array(),
			"categories" => array()
		);
		
		$lastmovie = $db->selectVal('SELECT id FROM movies ORDER BY id DESC LIMIT 1')['id'];
		$movie['id'] = $lastmovie;
		
		$actors = $db->select('SELECT DISTINCT personId FROM movies_people WHERE movieId = ? AND staff >= 0', array($lastmovie));
		foreach ($actors as $a) {
			$movie['actors'][] = $a['personId'];
		}
		
		$directors = $db->select('SELECT DISTINCT personId FROM movies_people WHERE movieId = ? AND director = 1', array($lastmovie));
		foreach ($directors as $a) {
			$movie['directors'][] = $a['personId'];
		}
		
		$others = $db->select('SELECT DISTINCT personId FROM movies_people WHERE movieId = ? AND staff = -1 AND director = 0', array($lastmovie));
		foreach ($others as $a) {
			$movie['others'][] = $a['personId'];
		}
		
		$countries = $db->select('SELECT DISTINCT country FROM movies_countries WHERE movieId = ?', array($lastmovie));
		foreach ($countries as $a) {
			$movie['countries'][] = $a['country'];
		}
		
		$cats = $db->select('SELECT DISTINCT categoryId FROM movies_categories WHERE movieId = ?', array($lastmovie));
		foreach ($cats as $a) {
			$movie['categories'][] = $a['categoryId'];
		}
		
		return $movie;
	}

    function echoResult($result) {
        echo json_encode($result);
    }
?>