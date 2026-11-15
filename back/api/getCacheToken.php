<?php
	include '../php/datamodel.php';

    global $db;
	
	$lastId = $db->selectVal('SELECT id FROM movies ORDER BY id DESC LIMIT 1');

    echoResult($lastId['id']);
?>