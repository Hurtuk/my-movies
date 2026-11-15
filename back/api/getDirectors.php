<?php
	include '../php/datamodel.php';

    global $db;
	
	$data = $db->select("SELECT DISTINCT p.id, p.firstname, p.lastname
						FROM movies_people mp
						INNER JOIN people p
						ON p.id = mp.personId
						WHERE mp.director = 1
						ORDER BY p.lastname, p.firstname");

    echoResult($data);
?>