<?php
	include '../../php/datamodel.php';

    global $db;
	
	$type = $_GET['arg0'];
	
	$lastmovie = getLastMovie();
	
	$res = $db->select('SELECT p.id
							FROM people p
							INNER JOIN movies_people mp
							ON mp.personId = p.id
							WHERE '.($type === 'acts' ? 'mp.staff = 1' : 'mp.director = 1').'
							AND died = ?
							GROUP BY p.id', array(date('Y')));
							
	$res = array_map(function($item) use ($lastmovie) { $item["justseen"] = in_array($item, $lastmovie['actors']); return $item; }, $res);
							
    echoResult($res);
?>