import { SimpleEntity } from "./simple-entity";
import { Person } from "./person";
import { OscarNomination } from "./oscarNomination";
import { Observable } from 'rxjs/internal/Observable';

export class Movie {
	id: number;
	title: string;
	titleFr: string;
	categories: SimpleEntity[];
	directors: Person[];
	actors: Person[];
	year: number;
	length: number | null;
	mark: number | null;
	owned: boolean | null;
	cinema: boolean | null;
	saga: SimpleEntity;
	seenDate: Date | null;
	otherActors: Observable<Person[]>;
	oscars: Observable<OscarNomination[]>;
}