<?php
	require_once __DIR__."/../php/php_header.php";
	
	global $db;
	
	if (isset($_POST['id_indi'])) {
		move_uploaded_file($_FILES['photo']['tmp_name'], __DIR__."/../images/people/" . $_POST['id_indi'] . ".jpg");
	}
	
	$req = "SELECT DISTINCT i.id, i.lastname, i.firstname
			FROM movies_people j
			INNER JOIN people i
			ON i.id = j.personId
			WHERE j.staff = 1 OR j.director != 0
			ORDER BY lastname, firstname";
	$indi = $db->select($req);
	$i = 0;
	while (file_exists(__DIR__."/../images/people/" . $indi[$i]['id'] . ".jpg")) {
		$i++;
	}
	$indi = $indi[$i];
	
	if ($indi) { ?>
		<div id="add_picture">
			<div id="etapes">Etape 5/5</div>
			<div id="indi_changing">
				<a target="_blank"
					href="https://www.google.fr/search?q=<?php echo $indi['firstname']."+".$indi['lastname']; ?>">
						<?php echo $indi['firstname']." ".$indi['lastname']; ?>
				</a>
			</div>
			<div>
				<form method="post" action="addpicture.php" onsubmit="return add_movie(this, 'new_movie/addpicture.php', '../new_movie/addpicture.php');"
					enctype="multipart/form-data">
					<input type="hidden" name="id_indi" value="<?php echo $indi['id']; ?>" />
					<input type="file" name="photo" />
					<input type="submit" value="Envoyer !" />
				</form>
			</div>
		</div><?php
	} else {
		header('Location: ../add_movie.php');
	}
?>