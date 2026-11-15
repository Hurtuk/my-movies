<?php
	include '../php/datamodel.php';

    global $db;

	$movieId = $_GET['arg0'];

	// Seconds rôles du film qui sont des acteurs "connus" : au moins un premier
	// rôle, ou au moins 10 films. Le critère n'est évalué que pour les acteurs
	// du film, au lieu de construire la liste de tous les acteurs connus de la
	// base puis de la réinjecter dans un IN (...).
	$people = $db->select("SELECT p.id, p.firstname, p.lastname, mp.role
		FROM movies_people mp
		INNER JOIN people p
		ON p.id = mp.personId
		INNER JOIN (
			SELECT k.personId
			FROM movies_people k
			WHERE k.staff >= 0
			AND k.personId IN (SELECT c.personId FROM movies_people c WHERE c.movieId = ? AND c.staff = 0)
			GROUP BY k.personId
			HAVING SUM(k.staff) > 0 OR COUNT(k.movieId) >= 10
		) known
		ON known.personId = mp.personId
		WHERE mp.staff = 0
		AND mp.movieId = ?
		ORDER BY p.lastname, p.firstname, mp.id",
		array($movieId, $movieId));

    echoResult($people);
?>
