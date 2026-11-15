<?php
	require_once __DIR__."/../php/php_header.php";
	
	global $db;
?>
<div id="add_nat">
	<div id="etapes">Etape 3/5</div>
	<?php
		$req = "SELECT id, titleFr, year FROM movies
				WHERE id NOT IN (SELECT DISTINCT movieId FROM movies_countries)
				ORDER BY id";
		$res = $db->selectVal($req);
	?>
	<div id="add_nat_title"><a href="https://www.google.fr/search?q=<?php echo str_replace(" ", "%20", $res['titleFr']); ?>%20imdb" target="_blank"><?php echo $res['titleFr'].' ('.$res['year'].')'; ?></a></div>
	<form method="post" action="php/add_natio.php" onsubmit="return add_movie(this, 'php/add_natio.php', '../new_movie/addyears.php');">
		<input type="hidden" name="film" value="<?php echo $res['id']; ?>" />
		<div>
		<input type="submit" value="Envoyer !" />
			<table>
				<tr>
					<td>
						<?php
							$i = 0;
							$req = "SELECT DISTINCT country AS nat, COUNT(*) AS cnt FROM movies_countries GROUP BY country ORDER BY cnt DESC, country ASC";
							$res = $db->select($req);
							foreach ($res as $nat) {
								echo '<input type="hidden" name="nom'.$i.'" value="'.$nat['nat'].'" />
									<input type="checkbox" name="c'.$i.'" />'.$nat['nat'].' ('.$nat['cnt'].')<br />';
								$i++;
							}
						?>
					</td>
				</tr>
				<tr>
					<td>
						<div id="new_nat"></div>
						<div>Nouveau : <input type="text" id="new_nat_input" /><span onClick="ajouter(<?php echo $i; ?>)">Ajouter</span></div>
					</td>
				</tr>
			</table>
		</div>
		<input type="hidden" id="compteur" name="compteur" value="0" />
	</form>
</div>