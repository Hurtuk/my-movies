<?php
	/**
	 * Cache fichier des statistiques coûteuses (getCollabs, getCollabsDirs).
	 *
	 * Le résultat est stocké dans cache/<nom>.json avec l'empreinte des données
	 * dont il dépend ; il est recalculé dès que cette empreinte change.
	 *
	 * L'empreinte ne se limite pas à l'id du dernier film : l'ajout d'un film se
	 * fait en plusieurs étapes (add_movie.php insère le film et le casting,
	 * add_roles.php passe ensuite les premiers rôles à staff = 1, saga.php
	 * rattache le film à une saga). Elle couvre donc :
	 *   - movies_people : nombre de lignes, plus grand id, sommes de personId,
	 *     staff et director (ajout, suppression ou modification d'un rôle) ;
	 *   - movies : nombre de films, plus grand id (dernier film vu, qui sert au
	 *     « +1 ») et somme pondérée des sagaId (rattachement à une saga).
	 * Les deux requêtes se servent des index (idx_mp_movie couvre toutes les
	 * colonnes lues) : quelques millisecondes.
	 */

	function collabsFingerprint() {
		global $db;
		$mp = $db->selectVal('SELECT COUNT(*) AS n, MAX(id) AS maxId, SUM(personId) AS p,
			SUM(COALESCE(staff, -2) + 2) AS s, SUM(director) AS d
			FROM movies_people');
		$m = $db->selectVal('SELECT COUNT(*) AS n, MAX(id) AS maxId, SUM(id * COALESCE(sagaId, 0)) AS sg
			FROM movies');
		return md5(json_encode(array($mp, $m)));
	}

	/**
	 * Renvoie le résultat en cache pour $name si l'empreinte n'a pas changé,
	 * sinon appelle $compute(), stocke et renvoie son résultat.
	 * Une erreur d'écriture (droits, disque) n'empêche jamais la réponse.
	 */
	function cachedResult($name, $fingerprint, $compute) {
		$dir = __DIR__ . '/../cache';
		$file = "$dir/$name.json";

		$cached = @file_get_contents($file);
		if ($cached !== false) {
			$cached = json_decode($cached, true);
			if (is_array($cached) && ($cached['fingerprint'] ?? null) === $fingerprint) {
				header('X-Collabs-Cache: HIT');
				return $cached['data'];
			}
		}

		$data = $compute();

		if (is_dir($dir) || @mkdir($dir, 0755, true)) {
			// Écriture atomique : une requête concurrente lit l'ancien fichier
			// ou le nouveau, jamais un fichier à moitié écrit.
			$tmp = $file . '.' . getmypid() . '.tmp';
			if (@file_put_contents($tmp, json_encode(array('fingerprint' => $fingerprint, 'data' => $data))) !== false) {
				@rename($tmp, $file);
			} else {
				@unlink($tmp);
			}
		}
		header('X-Collabs-Cache: MISS');
		return $data;
	}
?>
