<?php
	include '../php/datamodel.php';

    global $db;
	
	$cats = $db->select("SELECT DISTINCT mc.country
						FROM movies_countries mc
						ORDER BY mc.country");

    echoResult($cats);
?>