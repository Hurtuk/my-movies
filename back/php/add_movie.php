<?php
	require_once __DIR__."/php_header.php";
	
	global $db;
	
	//$sd = split("/", $_POST['seen_date']);
	//$seen_date = $sd[2]."-".$sd[1]."-".$sd[0];
	if ($_POST['seen_date']) {
		$seen_date = date('Y-m-d', strtotime($_POST['seen_date']));
	} else {
		$seen_date = null;
	}
	$titreVF = $_POST['titreVF'];
	$titreVO = $_POST['titreVO'];
	$annee = $_POST['annee'];
	$genres = explode(", ", $_POST['genre']);
	$saga = $_POST['saga'];
	$new_saga = $_POST['new_saga'];
	$duree = $_POST['duree'];
	$realisateurs = $_POST['realisateur'];
	$acteurs = $_POST['acteurs'];
	$annee_oscars = $_POST['annee_oscars'];
	$oscars = $_POST['oscars'];
	$nominations = $_POST['nominations'];
	$note = $_POST['note'];
	$overview = $_POST['overview2'] ? $_POST['overview2'] : $_POST['overview'];
	
	// Fix overview characters
	/*$overview = preg_replace('/([^ ])([\?!:;])/', '${1} ${2}', $overview);
	$overview = preg_replace('/ ([\.,\)])/', '${1}', $overview);
	$overview = preg_replace('/([\.,\)])([^ ])/', '${1} ${2}', $overview);
	$overview = preg_replace('/\.\.\./', '…', $overview);
	$overview = preg_replace('/ "/', ' « ', $overview);
	$overview = preg_replace('/" /', ' » ', $overview);*/
	
	$dvd = ($_POST['dvd']) ? 1 : 0;
	$cine = ($_POST['cine']) ? 1 : 0;
	
	$tab_reals = array();
	$tab_acteurs = array();
	$tab_oscars = array();
	$indice_o = 0;
	$indice_reals = 0;
	$indice_acteurs = 0;
	
	//Insertion dans database_sagas _________________________________________________________________________________________________
	
		if ($new_saga) {
			$req1 = "INSERT INTO sagas (title) VALUES(?)";
			$args = array($new_saga);
			$db->update($req1, $args);
			
			$req2 = "SELECT id FROM sagas ORDER BY id DESC LIMIT 1";
			$res2 = $db->selectVal($req2);
			$saga = $res2['id'];
		}
		
	// _______________________________________________________________________________________________________________________________
	
	//Insertion dans database_movies _________________________________________________________________________________________________
	
		$req1 = "INSERT INTO movies (titleFr, title, year, length, mark, owned, cinema, sagaId, seenDate, overview)
										VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$args = array($titreVF, $titreVO, $annee, $duree, $note, $dvd, $cine, ($saga == "") ? null : $saga, $seen_date, $overview);
		$db->update($req1, $args);
		
		$req2 = "SELECT id FROM movies ORDER BY id DESC LIMIT 1";
		$res2 = $db->selectVal($req2);
		$id_film = $res2['id'];
		
		// Genres
		
		$i = 0;
		foreach ($genres as $g) {
			$genre = $db->selectVal("SELECT id FROM categories WHERE name = ?", array($g));
			$db->update("INSERT INTO movies_categories (main, movieId, categoryId) VALUES (?, ?, ?)", array(!$i ? 1 : 0, $id_film, $genre['id']));
			$i++;
		}
		
		move_uploaded_file($_FILES['jaquette']['tmp_name'], "../images/posters/" . $id_film . ".jpg");
		
	// _______________________________________________________________________________________________________________________________
	
	// Insertion des réalisateurs ____________________________________________________________________________________________________
	
		$liste_reals = explode("\r\n",$realisateurs);
		foreach($liste_reals as $real) {
			$real_prenom_nom = explode(", ",$real);
			$real_prenom = $real_prenom_nom[0];
			$real_nom = $real_prenom_nom[1];
			
			$req3 = "SELECT id FROM people WHERE lastname = ? AND firstname = ?";
			$args = array($real_nom, $real_prenom);
			$real_existant = $db->select($req3, $args);
			if (count($real_existant) == 0) { //Si le réalisateur n'existe pas dans la table
				$req4 = "INSERT INTO people (lastname, firstname) VALUES(?, ?)";
				$db->update($req4, $args);
			}			
			$real_existant = $db->selectVal($req3, $args);
			//Insertion dans database_jonction
			$req5 = "INSERT INTO movies_people (movieId, personId, role, staff, director)
											VALUES(?, ?, ?, ?, ?)";
			$args = array($id_film, $real_existant['id'], '', -1, 1);
			$db->update($req5, $args);
			$tab_reals[$indice_reals] = $real_prenom.' '.$real_nom;
			$indice_reals++;
		}
	//_________________________________________________________________________________________________________________________________
	
	
	// Insertion des acteurs __________________________________________________________________________________________________________
	
		$liste_acteurs = explode("\r\n",$acteurs);
		foreach($liste_acteurs as $acteur_role) {
			$acteur_role = str_replace('  ',' ',$acteur_role);
			$acteur_role = explode("\t...\t",$acteur_role);
			$role = $acteur_role[1];
			$acteur_prenom_nom = explode(', ',$acteur_role[0]);
			$acteur_prenom = $acteur_prenom_nom[0];
			$acteur_nom = $acteur_prenom_nom[1];
			
			$req6 = "SELECT id FROM people WHERE lastname = ? AND firstname = ?";
			$args = array($acteur_nom, $acteur_prenom);
			$acteur_existant = $db->select($req6, $args);
			if (count($acteur_existant) == 0) { // Si l'acteur n'est pas dans la table
				$req7 = "INSERT INTO people (lastname, firstname) VALUES(?, ?)";
				$db->update($req7, $args);
			}
			$acteur_existant = $db->selectVal($req6, $args);
			//Insertion dans database_jonction
			$req8 = "INSERT INTO movies_people (movieId, personId, role, staff, director)
											VALUES(?, ?, ?, ?, ?)";
			$args = array($id_film, $acteur_existant['id'], $role, 0, 0);
			$db->update($req8, $args);
			$tab_acteurs[$indice_acteurs] = $acteur_prenom.' '.$acteur_nom;
			$indice_acteurs++;
		}
	//_________________________________________________________________________________________________________________________________
	
	
	// Insertion des Oscars_____________________________________________________________________________________________________________
		
		//Obtenus___________________________________
		
			if ($oscars) {
				$oscars = explode("\r\n",$oscars);
				foreach($oscars as $oscar_obtenu) {
					$oscar_laureats = explode(' - ',$oscar_obtenu);
					$rec = $oscar_laureats[0];
					if (strpos($rec,'hanson')) {
						$objet = $oscar_laureats[1];
						$laureats = $oscar_laureats[2];
					} else {
						$objet = '';
						$laureats = $oscar_laureats[1];
					}
					
					//Enregistrement de l'Oscar
					$req9 = "SELECT id FROM oscars WHERE name = ? AND year = ?";
					$args = array($rec, $annee_oscars);
					$oscar_existant = $db->select($req9, $args);
					if (count($oscar_existant) == 0) {
						$req10 = "INSERT INTO oscars (name, year) VALUES(?, ?)";
						$db->update($req10, $args);
					}
					$oscar_existant = $db->selectVal($req9, $args);
					$id_oscar = $oscar_existant['id'];
					
					//Enregistrement des laureats
					$laureats = explode(', ',$laureats);
					foreach($laureats as $laureat) {
						$laureat = explode('!',$laureat);
						$laureat_prenom = $laureat[0];
						$laureat_nom = $laureat[1];
						$req11 = "SELECT id FROM people WHERE lastname = ? AND firstname = ?";
						$args = array($laureat_nom, $laureat_prenom);
						$laureat_existant = $db->select($req11, $args);
						
						//Insertion ou Update
						if (count($laureat_existant) == 0) {
							$req12 = "INSERT INTO people (lastname, firstname) VALUES(?, ?)";
							$db->update($req12, $args);
							$tab_oscars[$indice_o] = $laureat_prenom.' '.$laureat_nom;
							$indice_o++;
						}
						$laureat_existant = $db->selectVal($req11, $args);
						//Insertion dans jonction
						if (strpos($rec,'Acteur') OR strpos($rec,'Actrice') OR strpos($rec,'alisateur')) {
							if (strpos($rec,'Acteur') OR strpos($rec,'Actrice')) {
								$req13 = "UPDATE movies_people
									SET oscarId = ?, oscarDetail = role, oscarWon = 1
									WHERE movieId = ? AND personId = ? AND role != ''";
							} else {
								$req13 = "UPDATE movies_people
									SET oscarId = ?, oscarDetail = '', oscarWon = 1
									WHERE movieId = ? AND personId = ? AND director = 1";
							}
							$args = array($id_oscar, $id_film, $laureat_existant['id']);
						} else {
							$req13 = "INSERT INTO movies_people (movieId, personId, role, staff, director, oscarId, oscarDetail, oscarWon)
															VALUES(?, ?, '', -1, 0, ?, ?, 1)";
							$args = array($id_film, $laureat_existant['id'], $id_oscar, $objet);
						}
						$db->update($req13, $args);
					}
				}
			}
		
		//____________________________________
		
		// Nominations ________________________
		
			if ($nominations) {
				$nominations = explode("\r\n",$nominations);
				foreach($nominations as $oscar_nomin) {
					$oscar_laureats = explode(' - ',$oscar_nomin);
					$rec = $oscar_laureats[0];
					if (strpos($rec,'hanson')) {
						$objet = $oscar_laureats[1];
						$laureats = $oscar_laureats[2];
					} else {
						$objet = '';
						$laureats = $oscar_laureats[1];
					}
					
					//Enregistrement de l'Oscar
					$req9 = "SELECT id FROM oscars WHERE name = ? AND year = ?";
					$args = array($rec, $annee_oscars);
					$oscar_existant = $db->select($req9, $args);
					if (count($oscar_existant) == 0) {
						$req10 = "INSERT INTO oscars (name, year) VALUES(?, ?)";
						$db->update($req10, $args);
					}
					$oscar_existant = $db->selectVal($req9, $args);
					$id_oscar = $oscar_existant['id'];
					
					//Enregistrement des laureats
					$laureats = explode(', ',$laureats);
					foreach($laureats as $laureat) {
						$laureat = explode('!',$laureat);
						$laureat_prenom = $laureat[0];
						$laureat_nom = $laureat[1];
						$req11 = "SELECT id FROM people WHERE lastname = ? AND firstname = ?";
						$args = array($laureat_nom, $laureat_prenom);
						$laureat_existant = $db->select($req11, $args);
						
						//Insertion ou Update
						if (count($laureat_existant) == 0) {
							$req12 = "INSERT INTO people (lastname, firstname) VALUES(?, ?)";
							$db->update($req12, $args);
							$tab_oscars[$indice_o] = $laureat_prenom.' '.$laureat_nom;
							$indice_o++;
						}
						$laureat_existant = $db->selectVal($req11, $args);
						//Insertion dans jonction
						if (strpos($rec,'Acteur') OR strpos($rec,'Actrice') OR strpos($rec,'alisateur')) {
							if (strpos($rec,'Acteur') OR strpos($rec,'Actrice')) {
								$req13 = "UPDATE movies_people
									SET oscarId = ?, oscarDetail = role, oscarWon = 0
									WHERE movieId = ? AND personId = ? AND role != ''";
							} else {
								$req13 = "UPDATE movies_people
									SET oscarId = ?, oscarDetail = '', oscarWon = 0
									WHERE movieId = ? AND personId = ? AND director = 1";
							}
							$args = array($id_oscar, $id_film, $laureat_existant['id']);
						} else {
							$req13 = "INSERT INTO movies_people (movieId, personId, role, staff, director, oscarId, oscarDetail, oscarWon)
															VALUES(?, ?, '', -1, 0, ?, ?, 0)";
							$args = array($id_film, $laureat_existant['id'], $id_oscar, $objet);
						}
						$db->update($req13, $args);
					}
				}
			}
		//____________________________________________
		
	//__________________________________________________________________________________________________________________________
?>