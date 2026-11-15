<?php
	include '../php/datamodel.php';

    global $db;

	$count = max(1, min(100, (int) $_GET['arg0']));

	// On isole d'abord les $count derniers films (sous-requête "last"), puis on
	// fait les jointures sur ces seuls films. Avant, les jointures et le
	// GROUP BY portaient sur toute la base, le LIMIT n'intervenant qu'à la fin.
	$movies = $db->select("SELECT m.id, m.title, m.titleFr, m.year, m.length, m.owned, m.cinema, m.mark,
				GROUP_CONCAT(DISTINCT CONCAT(c.id, '_', c.name) ORDER BY mc.main DESC, c.name SEPARATOR ';') AS categories,
				GROUP_CONCAT(DISTINCT CONCAT(director.id, '_', director.firstname, '_', director.lastname) ORDER BY director.lastname, director.firstname SEPARATOR ';') AS directors,
				GROUP_CONCAT(DISTINCT CONCAT(mainactor.id, '_', mainactor.firstname, '_', mainactor.lastname, '_', mp2.role) ORDER BY mainactor.lastname, mainactor.firstname SEPARATOR ';') AS mainactors
			FROM (
				-- Mêmes conditions que les INNER JOIN ci-dessous : un film sans
				-- catégorie, réalisateur ou acteur principal est sauté, comme avant.
				SELECT lm.id FROM movies lm
				WHERE EXISTS (SELECT 1 FROM movies_categories x INNER JOIN categories xc ON xc.id = x.categoryId WHERE x.movieId = lm.id)
				AND EXISTS (SELECT 1 FROM movies_people x INNER JOIN people xp ON xp.id = x.personId WHERE x.movieId = lm.id AND x.director = 1)
				AND EXISTS (SELECT 1 FROM movies_people x INNER JOIN people xp ON xp.id = x.personId WHERE x.movieId = lm.id AND x.staff = 1)
				ORDER BY lm.id DESC
				LIMIT $count
			) last
			INNER JOIN movies m
			ON m.id = last.id
			INNER JOIN (
				movies_categories mc
				INNER JOIN categories c
				ON c.id = mc.categoryId
			)
			ON m.id = mc.movieId
			INNER JOIN (
				movies_people mp1
				INNER JOIN people director
				ON director.id = mp1.personId
				AND mp1.director = 1
			)
			ON mp1.movieId = m.id
			INNER JOIN (
				movies_people mp2
				INNER JOIN people mainactor
				ON mainactor.id = mp2.personId
				AND mp2.staff = 1
			)
			ON mp2.movieId = m.id
			GROUP BY m.id
			ORDER BY m.id DESC, m.seenDate DESC");

    echoResult($movies);
?>
