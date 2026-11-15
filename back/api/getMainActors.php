<?php
	include '../php/datamodel.php';

    global $db;
	
	$optional = $_GET['arg0'];
	
	$data = $db->select("SELECT DISTINCT p.lastname, p.firstname, p.id
						FROM movies_people mp
						INNER JOIN people p
						ON p.id = mp.personId
						WHERE mp.staff > 0 ".($optional ? "OR (mp.staff = 0 AND mp.personId IN ($optional))" : "")."
						ORDER BY p.lastname, p.firstname");

    echoResult($data);
?>