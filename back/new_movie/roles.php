<?php
	require_once __DIR__."/../php/php_header.php";
	
	global $db;
	
	$req = "SELECT m.id, m.titleFr, m.year
			FROM movies m, movies_people j
			WHERE j.movieId = m.id
			AND j.director = 0 AND j.staff >= 0
			GROUP BY j.movieId
			HAVING SUM(j.staff) = 0
			LIMIT 1";
	$film = $db->selectVal($req);
?>
<div id="add_roles">
	<div id="etapes">Etape 2/5</div>
	<div id="add_roles_title"><?php echo $film['titleFr'].' ('.$film['year'].')'; ?></div>
	<form method="post" action="php/add_roles.php" onsubmit="return add_movie(this, 'php/add_roles.php', '../new_movie/natio.php');">
		<input type="hidden" name="film" value="<?php echo $film['id']; ?>" />
		<table>
			<?php
				$req = "SELECT j.id, i.firstname, i.lastname, j.role
						FROM people AS i, movies_people AS j
						WHERE j.movieId = ? AND i.id = j.personId
						AND director = 0 AND staff = 0
						ORDER BY j.id";
				$args = array($film['id']);
				$res = $db->select($req, $args);
				foreach ($res as $indi) {
					?><tr>
						<td><input type="checkbox" name="j<?php echo $indi['id']; ?>" /></td>
						<td><?php echo $indi['firstname'].' '.$indi['lastname']; ?> : </td>
						<td><?php echo $indi['role']; ?></td>
					</tr><?php
				}
			?>
		</table>
		<input type="submit" value="Envoyer !" />
	</form>
</div>