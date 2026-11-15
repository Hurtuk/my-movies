<?php
	include './php/datamodel.php';

    global $db;
	
	if (isset($_POST['movieId'])) {
		$db->update("DELETE FROM movies_categories WHERE movieId = ?", array($_POST['movieId']));
		$db->update("INSERT INTO movies_categories (main, movieId, categoryId) VALUES (?, ?, ?)", array(1, $_POST['movieId'], $_POST['main']));
		foreach ($_POST['secondary'] as $s) {
			$db->update("INSERT INTO movies_categories (main, movieId, categoryId) VALUES (?, ?, ?)", array(0, $_POST['movieId'], $s));
		}
		$db->update("UPDATE movies SET updated = 1 where id = ?", array($_POST['movieId']));
	}
	
	$cnt = $db->select('SELECT updated FROM movies ORDER BY titleFr');
	
	$categories = $db->select("SELECT id, name FROM categories ORDER BY name");
	$movie = $db->selectVal('SELECT m.id, m.titleFr, m.title, m.year, mc.categoryId FROM movies m INNER JOIN movies_categories mc ON mc.movieId = m.id WHERE m.updated = 0 ORDER BY m.id');
?>
<!doctype html>
<html lang="fr">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>Mise à jour des genres</title>
		<style>
			ul {
				width: 60%;
			}
			label {
				display: block;
				line-height: 1.5em;
				cursor: pointer;
				padding: 2px;
			}
			label:hover {
				outline: 1px dashed gray;
			}
			input[type=checkbox], input[type=radio] {
				display: none;
			}
			:checked + label {
				background-color: lightgreen;
				font-weight: bold;
			}
			
			input[type=submit] {
				width: 100px;
				line-height: 3em;
				display: block;
				margin: 30px auto;
				height: 3em;
			}
		</style>
	</head>
	<body>
		<div>
			<h3>Faits</h3>
			<?php
				foreach ($cnt as $c) {
					?><span style="display: inline-block; width: 1px; height: 20px; background-color: <?php echo $c['updated'] ? 'red' : 'lightgray'; ?>"></span><?php
				}
			?>
		</div>
		<ul style="float: right; width: 220px; overflow-y: auto; max-height: 700px;">
			<?php
				$mov = $db->select("SELECT m.titleFr, GROUP_CONCAT(c.name) AS cat FROM movies m INNER JOIN movies_categories mc ON mc.movieId = m.id INNER JOIN categories c ON mc.categoryId = c.id WHERE m.updated = 1 GROUP BY m.id ORDER BY titleFr");
				foreach ($mov as $m) {
					?><li><?php echo $m['titleFr']; ?>
						<ul>
							<?php $cats = explode(',', $m['cat']);
							foreach ($cats as $c) {
								?><li><?php echo $c; ?></li><?php
							} ?>
						</ul>
					</li><?php
				}
			
			?>
		</ul>
		<form method="post" action="">
			<input type="hidden" name="movieId" value="<?php echo $movie['id']; ?>" />
			<div style="float: left; width: 182px; height: 268px; background-size: contain; background-repeat: no-repeat; background-image: url(./images/posters/<?php echo $movie['id']; ?>.jpg);"></div>
			<h1><?php echo $movie['titleFr']; ?></h1>
			<h2><?php echo $movie['title'].' ('.$movie['year'].')'; ?></h2>
			<h3>Genre principal</h3>
			<ul style="list-style-type: none; margin: 0; padding: 0; columns: 5">
				<?php
					foreach ($categories as $c) {
						?><li>
							<input id="maincat-<?php echo $c['id']; ?>" type="radio" name="main" value="<?php echo $c['id']; ?>" <?php if ($movie['categoryId'] == $c['id']) echo 'checked'; ?> />
							<label for="maincat-<?php echo $c['id']; ?>">
								<?php echo $c['name']; ?>
							</label>
						</li><?php
					}
				?>
			</ul>
			<h3>Genres secondaires</h3>
			<ul style="list-style-type: none; margin: 0; padding: 0; columns: 5">
				<?php
					foreach ($categories as $c) {
						?><li>
							<input id="cat-<?php echo $c['id']; ?>" type="checkbox" name="secondary[]" value="<?php echo $c['id']; ?>" />
							<label for="cat-<?php echo $c['id']; ?>">
								<?php echo $c['name']; ?>
							</label>
						</li><?php
					}
				?>
			</ul>
			<input type="submit" value="OK !" />
		</form>
	</body>
</html>