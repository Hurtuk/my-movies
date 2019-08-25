import { HttpClient } from '@angular/common/http';

import { Injectable } from '@angular/core';
import { UrlBuilderService } from './url-builder.service';

import { map } from 'rxjs/operators';
import { Movie } from '../entities/movie';
import { SimpleEntity } from '../entities/simple-entity';
import { Person } from '../entities/person';
import { MovieService } from './movie.service';
import { Subject } from 'rxjs/internal/Subject';
import { Observable } from 'rxjs/internal/Observable';
import { SearchPersonService } from './searchPerson.service';

@Injectable()
export class SearchMovieService {

	public results = new Subject<Movie[]>();

	constructor(
		private http: HttpClient,
		private urlBuilder: UrlBuilderService,
		private movieService: MovieService,
		private searchPersonService: SearchPersonService
	) {}

	public getCinemaMovies(): Observable<{year: number, movies: Movie[]}[]> {
		return this.http.get<{year: number, movies: Movie[]}[]>(this.urlBuilder.buildUrl('stats/getCinemaMovies'))
						.pipe(
							map(response =>	response.map(m => ({
									year: m.year,
									movies: m.movies.map(x => this.movieService.jsonToMovie(x))
								}))
							)
						);
	}

	public getSagas(): Observable<{id: number, title: string, mark: number, length: number, count: number, justseen: boolean, movies: Movie[], directors: Person[], actors: Person[]}[]> {
		return this.http.get<any[]>(this.urlBuilder.buildUrl('stats/getSagas'))
						.pipe(
							map(response => response.map(x => ({
								id: Number.parseInt(x.id),
								title: x.title,
								mark: Number.parseFloat(x.mark),
								length: Number.parseInt(x.length),
								count: Number.parseInt(x.count),
								justseen: MovieService.toBoolean(x.justseen),
								movies: x.movies.map(m => this.movieService.jsonToMovie(m)),
								directors: x.directors.map(d => this.movieService.jsonToPerson(d)),
								actors: x.actors.map(d => this.movieService.jsonToPerson(d))
							})))
						);
	}

	public search(titleFr: string,
				title: string,
				yearBefore: string,
				yearAfter: string,
				markBefore: string,
				markAfter: string,
				categories: SimpleEntity[],
				countries: SimpleEntity[],
				directors: Person[],
				actors: Person[],
				owned: boolean,
				cinema: boolean): Observable<any> {
		if (actors) {
			this.searchPersonService.getPeopleById(actors.map(p => p.id), 'act').subscribe();
		}
		if (directors) {
			this.searchPersonService.getPeopleById(directors.map(p => p.id), 'dir').subscribe();
		}
		return this.http.get<any[]>(this.urlBuilder.buildUrl(
			'searchMovies',
			titleFr,
			title,
			yearBefore,
			yearAfter,
			markBefore,
			markAfter,
			!categories ? "" : categories.map(x => x.id).join(","),
			!countries ? "" : countries.map(x => x.id).join(","),
			!directors ? "" : directors.map(x => x.id).join(","),
			!actors ? "" : actors.map(x => x.id).join(","),
			owned ? "1" : "",
			cinema ? "1" : "" ))
					.pipe(
						map(response => {
							this.results.next(
								response.map(m => this.movieService.jsonToMovie(m)));
							})
					);
	}

    public getActiveYears(): Observable<string[]> {
		return this.http.get<any[]>(this.urlBuilder.buildUrl('getActiveYears'))
						.pipe(
							map(response => response.map(x => x.year))
						);
	}

    public getCategories(): Observable<SimpleEntity[]> {
		return this.http.get<SimpleEntity[]>(this.urlBuilder.buildUrl('getCategories'));
	}

    public getCountries(): Observable<SimpleEntity[]> {
		return this.http.get<any[]>(this.urlBuilder.buildUrl('getCountries'))
						.pipe(
							map(response => response
								.map(x => {
									const item = new SimpleEntity();
									item.id = x.country;
									item.name = x.country;
									return item;
								}))
						);
	}

    public getDirectors(): Observable<Person[]> {
		return this.http.get<Person[]>(this.urlBuilder.buildUrl('getDirectors'));
	}

	public getActors(optionalActor: string): Observable<Person[]> {
		return this.http.get<Person[]>(this.urlBuilder.buildUrl('getMainActors', optionalActor));
	}
}
