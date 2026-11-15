<?php
	include '../php/datamodel.php';

    global $db;
	
	$movieId = $_GET['arg0'];
	
	$oscars = $db->select("SELECT o.id, o.year, o.name, mp.oscarDetail, mp.oscarWon,
							GROUP_CONCAT(p.id ORDER BY p.lastname, p.firstname SEPARATOR ';') AS ids,
							GROUP_CONCAT(p.firstname ORDER BY p.lastname, p.firstname SEPARATOR ';') AS firstnames,
							GROUP_CONCAT(p.lastname ORDER BY p.lastname, p.firstname SEPARATOR ';') AS lastnames
						FROM oscars o
						INNER JOIN (movies_people mp
							INNER JOIN people p
							ON p.id = mp.personId
						)
						ON mp.oscarId = o.id
						AND mp.movieId = ?
						GROUP BY o.id, mp.oscarDetail, mp.role
						ORDER BY mp.oscarWon DESC, o.name",
		array($movieId));

    echoResult($oscars);
?>