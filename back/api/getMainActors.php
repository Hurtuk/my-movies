<?php
	include '../php/datamodel.php';

    global $db;

	// Acteurs ayant au moins un premier rôle, plus éventuellement des acteurs
	// demandés explicitement (liste d'identifiants "1,2,3") même s'ils n'ont
	// que des seconds rôles. Seuls les identifiants numériques sont retenus et
	// passés en paramètres liés.
	$optional = array();
	foreach (explode(',', $_GET['arg0'] ?? '') as $v) {
		$v = trim($v);
		if (ctype_digit($v)) {
			$optional[] = (int) $v;
		}
	}
	$optional = array_values(array_unique($optional));

	$data = $db->select("SELECT DISTINCT p.lastname, p.firstname, p.id
						FROM movies_people mp
						INNER JOIN people p
						ON p.id = mp.personId
						WHERE mp.staff > 0 ".($optional ? "OR (mp.staff = 0 AND mp.personId IN (".implode(',', array_fill(0, count($optional), '?'))."))" : "")."
						ORDER BY p.lastname, p.firstname",
						$optional);

    echoResult($data);
?>
