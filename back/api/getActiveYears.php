<?php
	include '../php/datamodel.php';

    global $db;
	
	$years = $db->select("SELECT DISTINCT m.year
						FROM movies m
						ORDER BY m.year");

    echoResult($years);
?>