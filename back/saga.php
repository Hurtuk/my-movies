<?php
	require_once __DIR__."/php/php_header.php";
	
	if (isset($_POST['saga'])) {
		$db->maj("UPDATE movies SET sagaId = ? WHERE id = ?", array($_POST['saga'], $_POST['movie']));
	}	
?>
<html lang="fr">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="http://www.louiecinephile.fr/db_movies3/css/global.css?t=<?php echo filemtime(__DIR__."/../css/global.css"); ?>" media="screen" />
		<title>Ajouter un film</title>
		<style>button { display: block; width: 200px; height: 200px; cursor: pointer; } table { margin: 0 auto; }</style>
	</head>
	<body>
		<div id="body">
			<form method="post" action="">
				<p>Film : <select name="movie"><?php
					$films = $db->select("SELECT id, titleFr FROM movies WHERE sagaId IS NULL ORDER BY titleFr");
					foreach ($films as $f) {
						?><option value="<?php echo $f['id']; ?>"><?php echo $f['titleFr']; ?></option><?php
					}
				?></select></p>
				<p>Saga : <select name="saga"><?php
					$films = $db->select("SELECT id, title FROM sagas ORDER BY title");
					foreach ($films as $f) {
						?><option value="<?php echo $f['id']; ?>"><?php echo $f['title']; ?></option><?php
					}
				?></select></p>
				<input type="submit" value="Ok" />
			</form>
		</div>
	</body>
</html>