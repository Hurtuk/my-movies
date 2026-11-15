<?php
	include '../../php/datamodel.php';
	include '../../php/collabs.php';
	include '../../php/cache.php';

    global $db;

	error_reporting(0);

	// Duos acteur (au moins 2 premiers rôles) / réalisateur ayant travaillé
	// ensemble sur au moins 4 films, une saga comptant pour un seul film.
	// Calcul de ~1,5 s : mis en cache jusqu'au prochain changement des données
	// (cf. php/cache.php).
	$r = cachedResult('collabsDirs', collabsFingerprint(), function() {
		$lastmovie = getLastMovie();
		$pairs = computeCollabs(getCollabActors(2), getCollabDirectors(), $lastmovie['id']);

		$r = array();
		foreach ($pairs as $pair) {
			$dd = array(
				'act2' => $pair['a'],
				'act1' => $pair['b'],
				'cnt' => $pair['cnt']
			);
			if ($pair['justseen']) {
				$dd['justseen'] = true;
			}
			$r[] = $dd;
		}
		return $r;
	});

    echoResult($r);
?>
