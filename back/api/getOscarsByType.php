<?php
	include '../php/datamodel.php';

    global $db;

	$type = $_GET['arg0'];

	$oscars = $db->select("SELECT o.id, o.year, o.name, o.candidates FROM oscars o WHERE o.name LIKE CONCAT('%', ?) ORDER BY o.year DESC", array($type));

	echoResult(addOscarNominations($oscars));
?>
