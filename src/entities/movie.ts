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
	length: number;
	mark: number;
	owned: boolean;
	cinema: boolean;
	saga: SimpleEntity;
	seenDate: Date;
	otherActors: Observable<Person[]>;
	oscars: Observable<OscarNomination[]>;
}