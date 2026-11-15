<?php
	include '../php/datamodel.php';

    global $db;
	
	$oscars = $db->select("SELECT DISTINCT o.name FROM oscars o ORDER BY o.name ASC");
	
	$result = array();
	foreach ($oscars as $o) {
		$result[] = $o['name'];
	}

    echoResult($result);
?>