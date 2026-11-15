<?php
	require_once __DIR__."/php/php_header.php";
?>
<!doctype html>
<html lang="fr">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="https://www.louiecinephile.fr/db_movies3/css/global.css?t=<?php echo filemtime(__DIR__."/../db_movies3/css/global.css"); ?>" media="screen" />
		<script type="text/javascript" src="https://www.louiecinephile.fr/db_movies3/js/jquery-1.10.2.min.js"></script>
		<script type="text/javascript" src="https://www.louiecinephile.fr/db_movies3/js/add_movie.js?t=<?php echo filemtime(__DIR__."/../db_movies3/js/add_movie.js"); ?>"></script>
		<script type="text/javascript" src="https://www.louiecinephile.fr/db_movies3/js/add_nat.js?t=<?php echo filemtime(__DIR__."/../db_movies3/js/add_nat.js"); ?>"></script>
		<title>Ajouter un film</title>
		<style>
			input[type=text], textarea {
				width: 250px;
			}
		</style>
	</head>
	<body>
		<div id="content">
		<div id="addformpage">
			<div id="etapes">Etape 1/5</div>
			<form method="post" action="php/add_movie.php" onsubmit="return add_movie(this, 'php/add_movie.php', '../new_movie/roles.php');"
					enctype="multipart/form-data">
				<table>
					<tr><td>Date : </td><td><input type="date" id="seen_date" name="seen_date" value="<?= date('Y-m-d') ?>"/></td></tr>
					<tr><td>Titre VF : </td><td><input type="text" spellcheck="false" id="titreVF" name="titreVF"/></td></tr>
					<tr><td>Titre VO : </td><td><input type="text" spellcheck="false" id="titreVO" name="titreVO"/></td></tr>
					<tr><td>Année : </td><td><input type="text" id="annee" name="annee"/></td></tr>
					<tr><td>Lien jaquette : </td><td><a id="jaquette-search" target="_blank" href="">Google Images</a></td></tr>
					<tr><td>Jaquette IMDB : </td><td><input type="file" id="jaquette" name="jaquette"/></td></tr>
					<tr><td>Genre : </td><td><input type="text" id="genre" name="genre"/></td></tr>
					<tr><td>Saga : </td><td><select id="saga" name="saga"><option></option><?php
						global $db;
						$sagas = $db->select("SELECT id, title FROM sagas ORDER BY title");
						foreach ($sagas as $s) {
							?><option value="<?php echo $s['id']; ?>"><?php echo $s['title']; ?></option><?php
						}
					?></select></td><td><input type="text" id="new_saga" name="new_saga" /></td>
					<tr><td>Durée : </td><td><input type="text" id="duree" name="duree"/></td></tr>
					<tr><td>Réalisateur : </td><td><textarea rows="2" spellcheck="false" id="realisateur" name="realisateur"></textarea></td></tr>
					<tr><td>Lien acteurs : </td><td><a id="lien-actors" target="_blank" href="">TMDB</a></td></tr>
					<tr><td>Acteurs : </td><td><textarea spellcheck="false" rows="15" id="acteurs" name="acteurs"></textarea></td></tr>
					<tr><td>Année des Oscars : </td><td><input type="text" id="annee_oscars" name="annee_oscars"/></td></tr>
					<tr><td>Oscars : </td><td><textarea rows="2" spellcheck="false" id="oscars" name="oscars"></textarea></td></tr>
					<tr><td>Nominations : </td><td><textarea rows="2" spellcheck="false" id="nominations" name="nominations"></textarea></td></tr>
					<tr><td>Note : </td><td><input type="text" id="note" name="note"/></td></tr>
					<tr><td>Kodi : </td><td><input type="checkbox" id="dvd" name="dvd"/></td></tr>
					<tr><td>Vu au ciné : </td><td><input type="checkbox" id="cine" name="cine"/></td></tr>
					
					<tr><td>Résumé : </td><td colspan="2">
						<a style="color: red; cursor: pointer; font-weight: bold" id="search-synopsis">Rechercher</a>
						<div></div>
						<a target="_blank" href="https://www.themoviedb.org/?language=fr">The Movie DB</a>
					</td></tr>
					
					<tr><td colspan="2"><input type="submit" value="Enregistrer le film"/></td></tr>
				</table>
			</form>
		</div>
		<script type="text/javascript">
			$(function() {
				var did = false;
				
				function boxSize(box) {
					var max = 0, content = $(box).val().split("\n");
					for (var c in content) {
						if (content[c].length > max) {
							max = content[c].length;
						}
					}
					$(box).css('width', (9 * max + 20) + "px");
					$(box).attr('rows', content.length + 1);
				}
				
				function format(content) {
					var result = [];
					var regex = /(?:(.+)\n)\1?\n(.+)/g;
					var actors = content.match(regex);
					
					while ((data = regex.exec(content)) !== null) {
						var person = data[1];
						var role = data[2];
						if (role.indexOf("archive footage") === -1) {
							result.push(person + "\t...\t" + role);
						}
					}
					
					// Put commas
					for (var i = 0; i < result.length; i++) {
						var actor = result[i].substr(0, result[i].indexOf("\t")),
							rest = result[i].substr(result[i].indexOf("\t"));
						var names = actor.split(" ").map(val => String(val).charAt(0).toUpperCase() + String(val).slice(1));
						if (names.length == 2) {
							names[1][0] = ("" + names[1][0]).toUpperCase();
							result[i] = names[0] + ', ' + names[1] + rest;
						} else {
							if (names[1]?.match("/^[A-Z]\.$/")) { // TODO ça marche pas
								result[i] = names[0] + ' ' + names[1] + ', ' + names[2] + rest;
							} else if (names[1] == 'Van' || names[1] == 'Von' || names[1] == 'De' || names[2] == "Jr.") {
								result[i] = names[0] + ', ' + names[1] + ' ' + names[2] + rest;
							} else {
								result[i] = "\t" + result[i];
							}
						}
					}
					
					return result.join("\n");
				}
				
				$('#acteurs').change(function() {
					if (!did) {
						var val = $(this).val();
						val = format(val);
						$(this).val(val);
						boxSize(this);
						did = true;
					}
				});
				
				$('#titreVF').change(function() {
					$('#lien-actors').prop('href', 'https://www.themoviedb.org/search/movie?language=fr&query=' + $(this).val().replace(' ', '+'));
				});
				
				$('#titreVO').change(function() {
					$('#jaquette-search').prop('href', 'https://www.google.com/search?q=' + $(this).val().replace(' ', '+') + '&sca_esv=85427b9423f451d4&udm=2&sxsrf=AE3TifOIXhuKpUpgbBXwoQUhd4bPfNWbeA:1756743763739&source=lnt&tbs=isz:m&sa=X&ved=2ahUKEwiZ5_6__LePAxWeVKQEHf-1AMwQpwV6BAgEEAc&biw=1920&bih=911&dpr=1');
				});
				
				$('#search-synopsis').click(function() {
					$.get("https://api.themoviedb.org/3/search/movie?api_key=<?= urlencode(tmdbApiKey()) ?>&language=fr-FR&query=" + $("#titreVO").val() + "&year=" + $("#annee").val(), function(data) {
						if (data.total_results == 0) {
							$.get('https://api.themoviedb.org/3/search/movie?api_key=<?= urlencode(tmdbApiKey()) ?>&language=fr-FR&query=' + $("#titreVO").val(), function(data2) {
								for (var res of data2.results) {
									if (res.overview) {
										$('#search-synopsis').next().append('<div><input type="radio" name="overview"'+(data2.total_results == 1 ? ' checked="checked"' : '')+' value="'+res.overview.replace('"', '&quot;')+'" />'+res.overview+'</div>');
									}
								}
								$('#search-synopsis').next().append('<textarea spellcheck="false" name="overview2"></textarea>');
							});
						} else {
							for (var res of data.results) {
								if (res.overview) {
									$('#search-synopsis').next().append('<div><input type="radio" name="overview"'+(data.total_results == 1 ? ' checked="checked"' : '')+' value="'+res.overview.replace('"', '&quot;')+'" />'+res.overview+'</div>');
								}
							}
							$('#search-synopsis').next().append('<textarea spellcheck="false" name="overview2"></textarea>');
						}
					});
				});
			});
		</script>
	</body>
</html>