<?php
	include '../../php/datamodel.php';

    global $db;
	
	$type = $_GET['arg0'];
	
	$lastmovie = getLastMovie();
	
	$res = array();
	
	$r = $db->selectVal('SELECT GROUP_CONCAT(DISTINCT p.id) AS ids, born
							FROM people p
							INNER JOIN movies_people mp
							ON mp.personId = p.id
							WHERE '.($type === 'acts' ? 'mp.staff = 1' : 'mp.director = 1').'
							AND died IS NULL AND born IS NOT NULL && born != 0
							AND sex = "F"
							GROUP BY born
							ORDER BY born
							LIMIT 1');
	$res['fold'] = array_map(function($item) use ($r, $lastmovie) { return ['id' => $item, 'born' => $r['born'], "justseen" => in_array($item, $lastmovie['actors'])];}, explode(',', $r['ids']));
	
	$r = $db->selectVal('SELECT GROUP_CONCAT(DISTINCT p.id) AS ids, born
							FROM people p
							INNER JOIN movies_people mp
							ON mp.personId = p.id
							WHERE '.($type === 'acts' ? 'mp.staff = 1' : 'mp.director = 1').'
							AND died IS NULL AND born IS NOT NULL && born != 0
							AND sex = "F"
							GROUP BY p.id
							ORDER BY born DESC
							LIMIT 1');
	$res['fyoung'] = array_map(function($item) use ($r, $lastmovie) { return ['id' => $item, 'born' => $r['born'], "justseen" => in_array($item, $lastmovie['actors'])]; }, explode(',', $r['ids']));
	
	$r = $db->selectVal('SELECT GROUP_CONCAT(DISTINCT p.id) AS ids, born
							FROM people p
							INNER JOIN movies_people mp
							ON mp.personId = p.id
							WHERE '.($type === 'acts' ? 'mp.staff = 1' : 'mp.director = 1').'
							AND died IS NULL AND born IS NOT NULL && born != 0
							AND sex = "M"
							GROUP BY p.id
							ORDER BY born
							LIMIT 1');
	$res['mold'] = array_map(function($item) use ($r, $lastmovie) { return ['id' => $item, 'born' => $r['born'], "justseen" => in_array($item, $lastmovie['actors'])]; }, explode(',', $r['ids']));
	
	$r = $db->selectVal('SELECT GROUP_CONCAT(DISTINCT p.id) AS ids, born
							FROM people p
							INNER JOIN movies_people mp
							ON mp.personId = p.id
							WHERE '.($type === 'acts' ? 'mp.staff = 1' : 'mp.director = 1').'
							AND died IS NULL AND born IS NOT NULL && born != 0
							AND sex = "M"
							GROUP BY p.id
							ORDER BY born DESC
							LIMIT 1');
	$res['myoung'] = array_map(function($item) use ($r, $lastmovie) { return ['id' => $item, 'born' => $r['born'], "justseen" => in_array($item, $lastmovie['actors'])]; }, explode(',', $r['ids']));
							
    echoResult($res);
?>