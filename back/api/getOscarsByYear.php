<?php
	include '../php/datamodel.php';

    global $db;

	$year = $_GET['arg0'];

	$oscars = $db->select("SELECT o.id, o.year, o.name, o.candidates FROM oscars o WHERE o.year = ? ORDER BY o.name ASC", array($year));

	echoResult(addOscarNominations($oscars));
?>
