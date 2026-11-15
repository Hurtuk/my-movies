<?php
	require_once __DIR__."/../php/php_header.php";
	
	global $db;
	
	$req = $db->selectVal("SELECT i.id, i.lastname, i.firstname
						FROM movies_people j
						INNER JOIN people i
						ON j.personId = i.id
						WHERE (j.director != 0 OR j.staff > 0)
							AND born IS NULL
						GROUP BY i.id
						ORDER BY i.lastname, i.firstname");
						
	if (!$req) {
		header('Location: addpicture.php');
	} else { ?>
		<div id="add_years">
			<div id="etapes">Etape 4/5</div>
			<div id="indi_changing">
				<a target="_blank"
					href="https://www.google.fr/search?q=<?php echo $req['firstname']."+".$req['lastname']; ?>">
						<?php echo $req['firstname']." ".$req['lastname']; ?>
				</a>
			</div>
			<div>
				<form method="post" action="php/addyears.php" onsubmit="return add_movie(this, 'php/addyears.php', '../new_movie/addyears.php');">
					<input type="hidden" name="indi" value="<?php echo $req['id']; ?>" />
					<div>Né en <input type="text" name="born" /></div>
					<div>Mort en <input type="text" name="died" /></div>
					<input type="submit" value="Valider" />
				</form>
			</div>
		</div>
	<?php }
?>