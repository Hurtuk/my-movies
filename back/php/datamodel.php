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
		
		// Appelée par la plupart des endpoints de stats : on ne la calcule
		// qu'une fois par requête HTTP.
		static $cache = null;
		if ($cache !== null) {
			return $cache;
		}

		$lastmovie = $db->selectVal('SELECT id FROM movies ORDER BY id DESC LIMIT 1')['id'];
		$movie['id'] = $lastmovie;

		// Une seule lecture de movies_people pour les trois listes de personnes
		// (au lieu d'une par liste).
		$people = $db->select('SELECT personId, staff, director FROM movies_people WHERE movieId = ?', array($lastmovie));
		foreach ($people as $p) {
			if ($p['staff'] !== null && $p['staff'] >= 0) {
				$movie['actors'][] = $p['personId'];
			}
			if ($p['director'] == 1) {
				$movie['directors'][] = $p['personId'];
			}
			if ($p['staff'] !== null && $p['staff'] == -1 && $p['director'] == 0) {
				$movie['others'][] = $p['personId'];
			}
		}
		foreach (array('actors', 'directors', 'others') as $k) {
			$movie[$k] = array_values(array_unique($movie[$k]));
		}

		$countries = $db->select('SELECT DISTINCT country FROM movies_countries WHERE movieId = ?', array($lastmovie));
		foreach ($countries as $a) {
			$movie['countries'][] = $a['country'];
		}

		$cats = $db->select('SELECT DISTINCT categoryId FROM movies_categories WHERE movieId = ?', array($lastmovie));
		foreach ($cats as $a) {
			$movie['categories'][] = $a['categoryId'];
		}

		$cache = $movie;
		return $movie;
	}

	/**
	 * Ajoute à chaque oscar ses lauréats ('winners') et ses nommés ('nominees').
	 * Une seule requête pour tous les oscars, au lieu de deux par oscar.
	 */
	function addOscarNominations($oscars) {
		global $db;

		if (!$oscars) {
			return $oscars;
		}

		$index = array();
		foreach ($oscars as $k => $o) {
			$oscars[$k]['winners'] = array();
			$oscars[$k]['nominees'] = array();
			$index[$o['id']] = $k;
		}

		$ids = implode(',', array_map('intval', array_keys($index)));
		$rows = $db->select("SELECT mp.oscarId, mp.oscarWon, m.id, m.titleFr, mp.oscarDetail,
					GROUP_CONCAT(DISTINCT CONCAT(p.id, '_', p.firstname, '_', p.lastname)
								 ORDER BY p.lastname, p.firstname SEPARATOR ';') AS nominees
				FROM movies_people mp
				INNER JOIN people p
				ON p.id = mp.personId
				INNER JOIN movies m
				ON m.id = mp.movieId
				WHERE mp.oscarId IN ($ids) AND mp.oscarWon IN (0, 1)
				GROUP BY mp.oscarId, mp.oscarWon, m.id, mp.oscarDetail
				ORDER BY mp.oscarId, mp.oscarWon, m.id, mp.oscarDetail");

		foreach ($rows as $r) {
			$k = $index[$r['oscarId']];
			$list = $r['oscarWon'] == 1 ? 'winners' : 'nominees';
			$oscars[$k][$list][] = array(
				'id' => $r['id'],
				'titleFr' => $r['titleFr'],
				'oscarDetail' => $r['oscarDetail'],
				'nominees' => explode(';', $r['nominees'])
			);
		}

		return $oscars;
	}

    function echoResult($result) {
        echo json_encode($result);
    }
?>