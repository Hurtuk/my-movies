<?php
	include '../../php/datamodel.php';
	include '../../php/collabs.php';

    global $db;

	error_reporting(0);

	$lastmovie = getLastMovie();

	// Duos d'acteurs (au moins 2 premiers rôles chacun) ayant joué ensemble
	// dans au moins 4 films, une saga comptant pour un seul film.
	$actors = getCollabActors(2);

	$r = array();
	foreach (computeCollabs($actors, $actors, $lastmovie['id']) as $pair) {
		$dd = array(
			'act1' => $pair['a'],
			'act2' => $pair['b'],
			'cnt' => $pair['cnt']
		);
		if ($pair['justseen']) {
			$dd['justseen'] = true;
		}
		$r[] = $dd;
	}

    echoResult($r);
?>
