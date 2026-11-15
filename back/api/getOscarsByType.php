<?php
	include '../php/datamodel.php';

    global $db;
	
	$type = $_GET['arg0'];
	
	$oscars = $db->select("SELECT o.id, o.year, o.name, o.candidates FROM oscars o WHERE o.name LIKE '%".str_replace("'", "''", $type)."' ORDER BY o.year DESC");
	
	$req = "SELECT m.id, m.titleFr, mp.oscarDetail, 
					GROUP_CONCAT(DISTINCT CONCAT(p.id, '_', p.firstname, '_', p.lastname)
								 ORDER BY p.lastname, p.firstname SEPARATOR ';') AS nominees
				FROM movies_people mp
				INNER JOIN people p
				ON p.id = mp.personId
				INNER JOIN movies m
				ON m.id = mp.movieId
				WHERE oscarId = ? AND oscarWon = ?
				GROUP BY m.id, mp.oscarDetail";
				
	foreach ($oscars as $k => $o) {
		// Winners
		$oscars[$k]['winners'] = $db->select($req, array($o['id'], 1));
		foreach ($oscars[$k]['winners'] as $kk => $oo) {
			$oscars[$k]['winners'][$kk]['nominees'] = explode(';', $oo['nominees']);
		}
		// Nominees
		$oscars[$k]['nominees'] = $db->select($req, array($o['id'], 0));
		foreach ($oscars[$k]['nominees'] as $kk => $oo) {
			$oscars[$k]['nominees'][$kk]['nominees'] = explode(';', $oo['nominees']);
		}
	}

    echoResult($oscars);
?>