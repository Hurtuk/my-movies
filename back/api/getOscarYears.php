<?php
	include '../php/datamodel.php';

    global $db;
	
	$oscars = $db->select("SELECT DISTINCT o.year FROM oscars o ORDER BY o.year DESC");
	
	$result = array();
	foreach ($oscars as $o) {
		$result[] = $o['year'];
	}

    echoResult($result);
?>