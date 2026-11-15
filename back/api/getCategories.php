<?php
	include '../php/datamodel.php';

    global $db;
	
	$cats = $db->select("SELECT c.id, c.name
						FROM categories c
						ORDER BY c.name");

    echoResult($cats);
?>