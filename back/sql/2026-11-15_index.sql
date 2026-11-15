-- Index et moteur InnoDB pour les jointures sur movies_people.
-- Jouée en prod le 2026-11-15 (MariaDB 10.11.19, 1 s) via un endpoint jetable protégé
-- par jeton, supprimé aussitôt après (le port 3306 est filtré par LWS).
--
-- Testé le 2026-11-15 sur le dump du 2026-09-03 (MariaDB 10.10 locale) :
--   getLatestMovies                 2,50 s -> 0,10 s (0,016 s avec la requête réécrite)
--   requête par oscar / par film    0,017 s -> 0,001 s (exécutées en boucle par getOscarsBy*, getSagas, getLastMovie)
--   getCinemaActors                 0,31 s -> 0,03 s
--   durée de la migration : < 2 s
--
-- movies_people n'avait qu'un index unique sur id : toute jointure par movieId,
-- personId ou oscarId parcourait les 136 000 lignes.

ALTER TABLE movies_people
	ENGINE = InnoDB,
	DROP INDEX id,
	ADD PRIMARY KEY (id),
	ADD INDEX idx_mp_movie (movieId, staff, director, personId),
	ADD INDEX idx_mp_person (personId, staff, director, movieId),
	ADD INDEX idx_mp_oscar (oscarId, oscarWon);

ALTER TABLE movies
	ENGINE = InnoDB,
	DROP INDEX id,
	ADD PRIMARY KEY (id),
	ADD INDEX idx_movies_saga (sagaId);

ALTER TABLE oscars
	ENGINE = InnoDB,
	DROP INDEX id,
	ADD PRIMARY KEY (id),
	ADD INDEX idx_oscars_year (year);

ALTER TABLE people ENGINE = InnoDB;
ALTER TABLE movies_countries ENGINE = InnoDB;

ANALYZE TABLE movies, movies_people, people, movies_countries, movies_categories, oscars, sagas;
