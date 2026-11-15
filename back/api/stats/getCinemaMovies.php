<?php

include '../../php/datamodel.php';

    global $db;

	$movies = $db->select("(SELECT id, titleFr, YEAR(seenDate) AS seen, 1 AS precise
							FROM movies
							WHERE cinema = 1 AND seenDate IS NOT NULL)
							UNION
							(SELECT id, titleFr, year, 0 AS precise
							FROM movies
							WHERE cinema = 1 AND seenDate IS NULL)
							ORDER BY id ASC");
	$byyear = array();
	foreach ($movies as $m) {
		if (!isset($byyear[$m['seen']])) {
			$byyear[$m["seen"]] = array();
		}
		$byyear[$m["seen"]][] = $m;
	}
	
	ksort($byyear);
	$byyear = array_reverse($byyear);
	
	function thereIsPrecise($array, $begin, $end) {
		for ($i = $begin; $i < $end; $i++) {
			if ($array[$i]['precise'] == "1") {
				return true;
			}
		}
		return false;
	}
	
	$byyear2 = array();
	foreach ($byyear as $k => $y) {
		$count = count($y);
		$n = 0;
		for ($i = 0; $i < $count; $i++) {
			if ($y[$i]['precise'] == "0" && $i > 3 * $count / 4 && !thereIsPrecise($y, $i + 1, $count)) {
				if (!$byyear2[$y[$i]['seen'] + 1]) {
					$byyear2[$y[$i]['seen'] + 1] = array();
				}
				array_splice($byyear2[$y[$i]['seen'] + 1], $n++, 0, array(array(
					"id" => $y[$i]['id'],
					"titleFr" => $y[$i]['titleFr']
				)));
			} else {
				if (!isset($byyear2[$y[$i]['seen']])) {
					$byyear2[$y[$i]['seen']] = array();
				}
				$byyear2[$y[$i]['seen']][] = array(
					"id" => $y[$i]['id'],
					"titleFr" => $y[$i]['titleFr']
				);
			}
		}
	}
	
	$byyear3 = array();
	foreach ($byyear2 as $year => $movies) {
		$byyear3[] = array(
			"year" => $year,
			"movies" => $movies
		);
	}

    echoResult($byyear3);
	
?>