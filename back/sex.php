<?php
	require_once __DIR__."/php/php_header.php";
?>
<!doctype html>
<html lang="fr">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="http://www.louiecinephile.fr/db_movies3/css/global.css?t=<?php echo filemtime(__DIR__."/../css/global.css"); ?>" media="screen" />
		<title>Ajouter un film</title>
		<style>button { display: block; width: 200px; height: 200px; cursor: pointer; } table { margin: 0 auto; }</style>
	</head>
	<body>
	<?php
		global $db;
		
		if ($_POST['indi']) {
			$db->update("UPDATE people SET sex = '".$_POST['sex']."' WHERE id = ".$_POST['indi']);
		}
		
		$req = $db->selectVal("SELECT i.id, i.lastname, i.firstname
							FROM people i
							INNER JOIN movies_people mp
							ON mp.personId = i.id
							WHERE sex IS NULL
							AND mp.staff >= 0
							GROUP BY i.id
							HAVING SUM(mp.staff) > 0
							ORDER BY i.lastname, i.firstname
							LIMIT 1");
		?>
		<div id="body">
			<?php if ($req) { ?>
				<h2><a target="_blank" href="https://www.google.fr/search?q=<?php echo $req['firstname']."+".$req['lastname']; ?>"><?php echo $req['firstname']." ".$req['lastname']; ?></a></h2>
				<div>
					<table>
						<tr>
							<td colspan="2">
								<img width="160" height="200" src="./images/people/<?php echo $req['id']; ?>.jpg" />
							</td>
						</tr>
						<tr>
							<td>
								<form method="post" action="">
									<input type="hidden" name="prenom" value="<?php echo $req['firstname']; ?>" />
									<input type="hidden" name="indi" value="<?php echo $req['id']; ?>" />
									<input type="hidden" name="sex" value="M" />
									<button width="200"><img src="images/male.png" height="200" /></button>
								</form>
							</td>
							<td>
								<form method="post" action="">
									<input type="hidden" name="prenom" value="<?php echo $req['firstname']; ?>" />
									<input type="hidden" name="indi" value="<?php echo $req['id']; ?>" />
									<input type="hidden" name="sex" value="F" />
									<button width="200"><img src="images/female.png" height="200" /></button>
								</form>
							</td>
						</tr>
					</table>
				</div>
			<?php } ?>
		</div>
	</body>
</html>