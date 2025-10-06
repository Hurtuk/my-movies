import { HttpClient } from '@angular/common/http';

import { Injectable } from '@angular/core';
import { UrlBuilderService } from './url-builder.service';

import { map } from 'rxjs/operators';
import { Movie } from '../entities/movie';
import { SimpleEntity } from '../entities/simple-entity';
import { Person } from '../entities/person';
import { OscarNomination } from '../entities/oscarNomination';
import { Observable } from 'rxjs/internal/Observable';

@Injectable({
  providedIn: 'root'
})
export class MovieService {

	private LATEST_COUNT = 7;

	constructor(
		private http: HttpClient,
		private urlBuilder: UrlBuilderService
	) {}

	public static formatLength(length: number): string {
		const min = Math.floor(length % 60);
		return Math.floor(length / 60) + 'h' + (!min ? '' : min < 10 ? '0' + min : min);
	}

	public static formatBigLength(length: number): string {
		const min = Math.floor(length % 60);
		length /= 60;
		const hours = Math.floor(length % 24);
		length /= 24;
		const days = Math.floor(length % 7);
		length /= 7;
		const weeks = Math.floor(length);
		return (weeks ? weeks + 's' : '')
			+ (days ? ' ' + days + 'j' : '')
			+ (hours ? ' ' + hours + 'h' : '')
			+ (min ? ' ' + min + 'mn' : '');
	}

    public getGlobalInfo(): Observable<{length: string, mark: number, count: number}> {
		return this.http.get<any>(this.urlBuilder.buildUrl('getGlobalInfo'))
						.pipe(
							map(response => ({
								length: MovieService.formatBigLength(Number.parseInt(response.length)),
								mark: Number.parseFloat(response.mark),
								count: Number.parseInt(response.cnt),
							}))
						);
	}

    public getLatestMovies(): Observable<Movie[]> {
		return this.http.get<any[]>(this.urlBuilder.buildUrl('getLatestMovies', this.LATEST_COUNT))
						.pipe(
							map(response => response.map(movie => this.jsonToMovie(movie)))
						);
	}

	public getOtherActors(movie: Movie): Observable<Person[]> {
		return this.http.get<any[]>(this.urlBuilder.buildUrl('getOtherActors', movie.id))
						.pipe(
							map(response => response.map(people => this.jsonToPerson(people)))
						);
	}

	public getOscars(movie: Movie): Observable<OscarNomination[]> {
		return this.http.get<any[]>(this.urlBuilder.buildUrl('getOscarsOfMovie', movie.id))
						.pipe(
							map(response => response.map(oscars => this.jsonToOscars(oscars, movie)))
						);
	}

	public getSagaDetails(sagaId: number): Observable<Movie[]> {
		return this.http.get<any[]>(this.urlBuilder.buildUrl('stats/getSagaDetails', sagaId))
						.pipe(
							map(response => response.map(movie => this.jsonToMovie(movie)))
						);
	}

	public jsonToOscars(json: any, movie: Movie | null = null): OscarNomination {
		const oscarNomination = new OscarNomination;
			oscarNomination.id = json.id;
			oscarNomination.year = json.year;
			oscarNomination.name = json.name;
			oscarNomination.detail = json.detail;
			oscarNomination.movie = movie ? movie : this.jsonToMovie(json.movie);
			if (json.ids) {
				oscarNomination.nominees = (json.ids as string).split(';')
					.map((id, index) => this.jsonToPerson({
						id: id,
						firstname: json.firstnames.split(';')[index],
						lastname: json.lastnames.split(';')[index],
						role: json.oscarDetail
					}));
			} else if (json.nominees) {
				oscarNomination.nominees = json.nominees;
			} else {
				oscarNomination.nominees = [];
			}
			oscarNomination.won = MovieService.toBoolean(json.oscarWon);
		return oscarNomination;
	}

	public jsonToPerson(json: any): Person {
		const person = new Person;
			person.id = json.id;
			person.firstname = json.firstname;
			person.lastname = json.lastname;
			person.justseen = MovieService.toBoolean(json.justseen);
			person.role = json.role;
			person.born = json.born;
			person.died = json.died;
			person.country = json.country;
			person.mark = json.mark;
			person.moviesCount = json.count;
			person.oscars = !json.oscars ? [] : json.oscars.map(o => this.jsonToOscars(o, null));
		return person;
	}

	public jsonToMovie(json: any): Movie {
		const movie = new Movie;
			movie.id = !json.id ? null : json.id,
			movie.title = !json.title ? null : json.title,
			movie.titleFr = !json.titleFr ? null : json.titleFr;
			movie.year = !json.year ? null : json.year;
			movie.length = !json.length ? null : Number.parseInt(json.length);
			movie.mark = !json.mark ? null : Number.parseFloat(json.mark);
			movie.owned = !json.owned ? null : MovieService.toBoolean(json.owned);
			movie.cinema = !json.cinema ? null : MovieService.toBoolean(json.cinema);
			movie.seenDate = !json.seenDate ? null : MovieService.toDate(json.seenDate);
			movie.categories = !json.categories ? [] : json.categories.split(';').map(x => {
				x = x.split('_');
				const entity = new SimpleEntity;
				entity.id = x[0];
				entity.name = x[1];
				return entity;
			});
			movie.directors = !json.directors ? [] : json.directors.split(';').map(x => {
				x = x.split('_');
				const person = new Person;
				person.id = x[0];
				person.firstname = x[1];
				person.lastname = x[2];
				return person;
			});
			movie.actors = !json.mainactors ? [] : json.mainactors.split(';').map(x => {
				x = x.split('_');
				const person = new Person;
				person.id = x[0];
				person.firstname = x[1];
				person.lastname = x[2];
				person.role = x[3];
				return person;
			});
			movie.otherActors = this.getOtherActors(movie);
			movie.oscars = this.getOscars(movie);
		return movie;
	}

	public static toBoolean(value: any) {
		if (typeof value === "string") {
			return !!Number.parseInt(value);
		}
		return !!value;
	}

	private static toDate(value: string) {
		return new Date(value);
	}
}
