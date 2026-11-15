<?php
	include_once __DIR__.'/datamodel.php';
	
	global $db;
	$db = new DB();
	
	function getNatio($id_indi) {
		global $db;
		$res = $db->select("SELECT mc.country AS nat, COUNT(DISTINCT mc.movieId) AS cnt
							FROM movies_people mp
							INNER JOIN movies_countries mc
							ON mc.movieId = mp.movieId
							WHERE mp.personId = ?
							GROUP BY mc.country
							ORDER BY cnt DESC", array($id_indi));
		//print_r($res);
		$cnt = count($res);
		$usa = getNatioCount("USA", $res);
		$gbr = getNatioCount("GBR", $res);
		
		//echo $cnt." ".$usa." ".$gbr;
		
		$max = array();
		for ($i = 0; $i < $cnt && $res[$i]['cnt'] == $res[0]['cnt']; $i++) {
			$max[] = $res[$i]['nat'];
		}
		if (!in_array("USA", $max)) {
			return $max[0];
		}
		for ($i = 0; $i < $cnt; $i++) {
			$r = $res[$i];
			if ($r['nat'] != "USA") {
				if ($r['nat'] == "GBR" && $r['cnt'] >= $usa / 2) {
					return "GBR";
				} else if ($r['cnt'] >= $usa * 0.75) {
					return $r['nat'];
				} else {
					break;
				}
			}
		}
		return "USA";
	}
	
	function getNatioCount($nat, $res) {
		foreach ($res as $r) {
			if ($r['nat'] == $nat) {
				return $r['cnt'];
			}
		}
	}
?>