<?php
	include '../php/datamodel.php';

    global $db;
	
	$data = $db->selectVal("SELECT COUNT(*) AS cnt, SUM(length) AS length, AVG(mark) AS mark FROM movies");

    echoResult($data);
?>