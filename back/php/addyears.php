<?php
	require_once __DIR__."/php_header.php";
	
	global $db;
	
	if ($_POST['indi']) {
		if ($_POST['died']) {
			$db->update("UPDATE people
				SET born = ?, died = ?
				WHERE id = ?",
				array($_POST['born'], $_POST['died'], $_POST['indi']));
		} else {
			$db->update("UPDATE people
				SET born = ?
				WHERE id = ?",
				array($_POST['born'], $_POST['indi']));
		}
	}
?>