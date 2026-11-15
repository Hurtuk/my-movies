<?php
	include '../../php/datamodel.php';
	include '../../php/collabs.php';

    global $db;

	error_reporting(0);

	$lastmovie = getLastMovie();

	// Duos acteur (au moins 2 premiers rôles) / réalisateur ayant travaillé
	// ensemble sur au moins 4 films, une saga comptant pour un seul film.
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

    echoResult($r);
?>
