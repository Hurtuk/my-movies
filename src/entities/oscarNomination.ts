import { Person } from "./person";
import { Movie } from "./movie";

export class OscarNomination {
	id: number;
	name: string;
	year: string;
	movie: Movie;
	nominees: Person[];
	detail: string;
	won: boolean;
}