<?php
	require_once __DIR__.'/php_header.php';
	
	global $db;
	
	if ($_POST['film']) {
		$id_film = $_POST['film'];

		//Insertion des existants
		$fait = false;
		for ($i = 0 ; $i <= 100 ; $i++) {
			if ($_POST['c'.$i]) {
				if ($fait) {
					$req .= ", ";
				} else {
					$req = "INSERT INTO movies_countries VALUES";
					$fait = true;
				}
				$req .= "(".$id_film.", '".$_POST['nom'.$i]."')";
			}
		}
		if ($req) {
			$db->update($req);
		}
	}
?>