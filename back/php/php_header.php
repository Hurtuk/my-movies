<?php
	header( 'content-type: text/html; charset=utf-8' );
	error_reporting(E_ALL);
	require_once __DIR__."/DB.class.php";
	global $db;
	$db = new DB();
	
	function tmdbApiKey() {
		$config = require __DIR__ . '/config.php';
		return $config['tmdb_api_key'];
	}
	
	function getFormattedLength($duree) {
		$res = "";
		if ($duree >= 60) {
			$res .= floor($duree / 60)."h";
			$duree %= 60;
		}
		if ($duree >= 10) {
			$res .= $duree;
		} else if ($duree > 0) {
			$res .= "0" . $duree;
		}
		return $res;
	}
	
	function getLength($minutes) {
		$s = "";
		$res = $minutes % 60;
		if ($res == 1) {
			$s = $res." minute";
		} else if ($res > 1) {
			$s = $res." minutes";
		}
		$minutes = floor($minutes / 60);
		$res = $minutes % 24;
		if ($res == 1) {
			$s = $res." heure ".$s;
		} else if ($res > 1) {
			$s = $res." heures ".$s;
		}
		$minutes = floor($minutes / 24);
		$res = $minutes % 7;
		if ($res == 1) {
			$s = $res." jour ".$s;
		} else if ($res > 1) {
			$s = $res." jours ".$s;
		}
		$minutes = floor($minutes / 7);
		$s = $minutes." semaines ".$s;
		return $s;
	}
	
	function average($liste, $crit) {
		$total = 0;
		foreach ($liste as $l) {
			$total += $l[$crit];
		}
		return $total / count($liste);
	}
	
	function getNatio($id_indi) {
		global $db;
		$res = $db->select("SELECT mc.nat, COUNT(DISTINCT mc.id) AS cnt
							FROM movies_people mp
							INNER JOIN movies_countries mc
							ON mc.id = mp.movieId
							WHERE mp.personId = ?
							GROUP BY mc.nat
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
	
	function isIn($id, $str, $separator) {
		return in_array($id, explode($separator, $str));
	}
?>