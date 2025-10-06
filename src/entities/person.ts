import { Movie } from './movie';
import { OscarNomination } from './oscarNomination';

export class Person {
	id: number;
	firstname: string;
	lastname: string;
	role: string;
	born: string;
	died: string;
	country: string;
	mark: number;
	moviesCount: number;
	justseen: boolean;

	// Extended
	movies: Movie[];
	directorMovies: Movie[];
	actorMovies: Movie[];
	otherMovies: Movie[];
	oscars: OscarNomination[];

	// Quick&dirty
	act1?: any;
	act2?: any;
	cnt?: any;
}