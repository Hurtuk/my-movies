<?php
	require_once __DIR__."/php/php_header.php";
?>
<!doctype html>
<html lang="fr">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="http://www.louiecinephile.fr/db_movies3/css/global.css?t=<?php echo filemtime(__DIR__."/../css/global.css"); ?>" media="screen" />
		<title>Ajouter un film</title>
	</head>
	<body>
	<?php
		global $db;
		
		if (isset($_POST['id'])) {
			$db->update('UPDATE movies SET overview = ? WHERE id = ?', array(str_replace('&quot;', '"', $_POST['overview']), $_POST['id']));
		}
		
		$req = $db->selectVal("SELECT m.id, m.title, m.titleFr, m.year from movies m where m.overview IS NULL ORDER BY m.title");
		
		$url = 'https://api.themoviedb.org/3/search/movie?api_key='.urlencode(tmdbApiKey()).'&language=fr-FR&query='.urlencode($req['title']).'&year='.$req['year'];
		$result = json_decode(file_get_contents($url));
		if ($result->total_results == 0) {
			$url = 'https://api.themoviedb.org/3/search/movie?api_key='.urlencode(tmdbApiKey()).'&language=fr-FR&query='.urlencode($req['title']);
			$result = json_decode(file_get_contents($url));
		}
		?>
		<div id="body">
			<?php if ($req) { ?>
				<div style="float: left; width: 145px; height: 218px; background-image: url(./images/posters/<?php echo $req['id']; ?>.jpg); background-size: cover;"></div>
				<h2><?php echo $req['title']; ?></h2>
				<h2><?php echo $req['titleFr']; ?></h2>
				<h3><?php echo $url; ?></h3>
				<div>
					<form method="post" action="">
						<input type="hidden" name="id" value="<?php echo $req['id']; ?>" />
						<?php
							if ($result->total_results == 0) {
								echo '<p>Aucun résultat.</p>';
								echo '<textarea name="overview"></textarea>';
							} else {
								foreach ($result->results as $res) {
									if ($res->overview) {
										echo '<div><input type="radio" name="overview"'.($result->total_results == 1 ? ' checked="checked"' : '').' value="'.str_replace('"', '&quot;', $res->overview).'" />'.$res->overview.'</div>';
									}
								}
							}
						?>
						<input type="submit" style="width: 300px; height: 100px;" value="Enregistrer" />
					</form>
					<?php if ($result->total_results > 0) { ?>
						<form method="post" action="">
							<input type="hidden" name="id" value="<?php echo $req['id']; ?>" />
							<textarea name="overview"></textarea>
							<input type="submit" style="width: 300px; height: 100px;" value="Enregistrer" />
						</form>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
	</body>
</html>