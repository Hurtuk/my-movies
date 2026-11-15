<?php
	include '../../php/datamodel.php';

    global $db;
	
	error_reporting(0);
	
	$MIN = 2;
	
	$by = "saga"; // Or something else
	
	$lastmovie = getLastMovie();
	
	/*$req = $db->select("SELECT personId AS id FROM movies_people WHERE staff = 1");
	$is = [];
	foreach ($req as $r) {
		$is[] = $r['id'];
	}
	$indis = implode(",", $is);*/
	
	function array_clone($array) {
		return array_map(function($element) {
			return ((is_array($element))
				? array_clone($element)
				: ((is_object($element))
					? clone $element
					: $element
				)
			);
		}, $array);
	}
	
	function equals($a1, $a2) {
		return $a1['id'] === $a2['id'];
	}
	
	function contains($movie, $otherMovies) {
		foreach ($otherMovies as $om) {
			if (equals($movie, $om)) {
				return true;
			}
		}
		return false;
	}
			
	$req = "SELECT p.id AS indi, GROUP_CONCAT(CONCAT(m.id, '_', IFNULL(m.sagaId, '')) SEPARATOR ',') AS movies
			FROM movies_people mp INNER JOIN people p ON mp.personId = p.id INNER JOIN movies m ON m.id = mp.movieId
			WHERE mp.staff != -1 GROUP BY p.id HAVING SUM(mp.staff) >= $MIN";
	
	$tab1 = $db->select($req);
	
	$tab1 = array_map(function($item) {
		return array(
			'indi' => $item['indi'],
			'movies' => array_map(function($m) { $d = explode('_', $m); return array('id' => $d[0], 'saga' => $d[1]); }, explode(',', $item['movies']))
		);
	}, $tab1);
	
	$tab2 = array_clone($tab1);
	
	$result = array();
	foreach ($tab1 as $a1) {
		foreach ($tab2 as $a2) {
			if ($a1['indi'] != $a2['indi'] && !isset($result[$a1['indi'].'_'.$a2['indi']]) && !isset($result[$a2['indi'].'_'.$a1['indi']])) {
				$result[$a1['indi'].'_'.$a2['indi']] = array('common' => []);
				foreach ($a1['movies'] as $m) {
					if (contains($m, $a2['movies'])) {
						$result[$a1['indi'].'_'.$a2['indi']]['common'][] = array_clone($m);
						if ($m['id'] === $lastmovie['id']) {
							$result[$a1['indi'].'_'.$a2['indi']]['justseen'] = true;
						}
					}
				}
			}
		}
	}
	
	if ($by === 'saga') {
		foreach ($result as &$r) {
			$different_sagas = [];
			$empties = 0;
			
			foreach ($r['common'] as $m) {
				if (!$m['saga']) {
					$empties++;
				} else if (!in_array($m['saga'], $different_sagas)) {
					$different_sagas[] = $m['saga'];
				}
			}
			
			$r['cnt'] = count($different_sagas) + $empties;
		}
		$result = array_filter($result, function($item) use ($MIN) { return $item['cnt'] >= 4; });
	} else {
		$result = array_filter($result, function($item) use ($MIN) { return count($item['common']) >= $MIN; });
		foreach ($result as &$r) {
			$r['cnt'] = count($r['common']);
		}
	}
	
	$r = array();
	foreach ($result as $k => $rr) {
		$d = explode('_', $k);
		$dd = array(
			'act1' => $d[0],
			'act2' => $d[1],
			'cnt' => $rr['cnt']
		);
		if ($rr['justseen']) {
			$dd['justseen'] = true;
		}
		$r[] = $dd;
	}
	
	usort($r, function($a, $b) {
		return $b['cnt'] - $a['cnt'];
	});
	
	
	
	/*$request = 'SELECT ia.personId AS indi1, id.personId AS indi2,
						GROUP_CONCAT(DISTINCT ia.movieId SEPARATOR ";") AS films,
						FLOOR(COUNT(DISTINCT '.(($by == "saga") ?
							  "im.sagaId) + SUM(im.sagaId IS NULL".(($param == "aa") ? ") / 2" : ")")
							: "ia.movieId)").') AS cnt
				FROM movies_people id
				INNER JOIN movies_people ia
				ON ia.movieId = id.movieId AND ia.personId != id.personId
				'.(($by == "saga") ? "INNER JOIN movies im ON im.id = ia.movieId" : "").'
				WHERE ia.personId IN ('.$indis.')
				AND ia.director = 0 AND ia.staff != -1';
	
	if ($param == "ar") {
		$request .= ' AND id.director = 1
					GROUP BY ia.personId, id.personId
					HAVING COUNT(DISTINCT '.(($by == "saga") ? "im.sagaId) + SUM(im.sagaId IS NULL)" : "ia.movieId)").' >= 4';
	} else {
		$request .= ' AND id.personId IN ('.$indis.')
					AND id.director = 0 AND id.staff != -1
					GROUP BY LEAST(ia.personId, id.personId), GREATEST(ia.personId, id.personId)
					HAVING cnt >= 5';
	}
	
	$request .= ' ORDER BY cnt DESC';
	
	echo $request;*/
	
	
	
	
	
	/*$req = $db->select($request);
	
	$type1 = ($param == "ar") ? "dirs" : "acts";
	
	$result = array();
	foreach ($req as $r) {
		$ex = explode(';', $r['films']);
		$result[] = array(
			'indi1' => array('type' => ($param === 'aa' ? 'acts' : 'dirs'), 'person' => $r['indi1']),
			'indi2' => array('type' => 'acts', 'person' => $r['indi2']),
			'count' => $r['cnt'],
			'justseen' => in_array($lastmovie['id'], $ex)
		);
	}*/
							
    echoResult($r);
?>