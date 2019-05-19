import { Http } from '@angular/http';

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
		private http: Http,
		private urlBuilder: UrlBuilderService,
		private movieService: MovieService,
		private searchPersonService: SearchPersonService
	) {}

	public getCinemaMovies(): Observable<{year: number, movies: Movie[]}[]> {
		return this.http.get(this.urlBuilder.buildUrl('stats/getCinemaMovies'))
						.pipe(
							map(response =>
									(response.json().data as {year: number, movies: Movie[]}[])
										.map(m => ({
											year: m.year,
											movies: m.movies.map(x => this.movieService.jsonToMovie(x))
										}))
							)
						);
	}

	public getSagas(): Observable<{id: number, title: string, mark: number, length: number, count: number, justseen: boolean, movies: Movie[], directors: Person[], actors: Person[]}[]> {
		return this.http.get(this.urlBuilder.buildUrl('stats/getSagas'))
						.pipe(
							map(response => (response.json().data as any[]).map(x => ({
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
		return this.http.get(this.urlBuilder.buildUrl(
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
								(response.json().data as any[])
									.map(m => this.movieService.jsonToMovie(m)));
							})
					);
	}

    public getActiveYears(): Observable<string[]> {
		return this.http.get(this.urlBuilder.buildUrl('getActiveYears'))
						.pipe(
							map(response => (response.json().data as any[]).map(x => x.year))
						);
	}

    public getCategories(): Observable<SimpleEntity[]> {
		return this.http.get(this.urlBuilder.buildUrl('getCategories'))
						.pipe(
							map(response => (response.json().data as any[])
										.map(x => {
											const item = new SimpleEntity();
											item.id = x.id;
											item.name = x.name;
											return item;
										}))
						);
	}

    public getCountries(): Observable<SimpleEntity[]> {
		return this.http.get(this.urlBuilder.buildUrl('getCountries'))
						.pipe(
							map(response => (response.json().data as any[])
										.map(x => {
											const item = new SimpleEntity();
											item.id = x.country;
											item.name = x.country;
											return item;
										}))
						);
	}

    public getDirectors(): Observable<Person[]> {
		return this.http.get(this.urlBuilder.buildUrl('getDirectors'))
						.pipe(
							map(response => (response.json().data as any[])
										.map(x => {
											const item = new Person();
											item.id = x.id;
											item.lastname = x.lastname;
											item.firstname = x.firstname;
											return item;
										}))
						);
	}

	public getActors(optionalActor: string): Observable<Person[]> {
		return this.http.get(this.urlBuilder.buildUrl('getMainActors', optionalActor))
						.pipe(
							map(response => (response.json().data as any[])
										.map(x => {
											const item = new Person();
											item.id = x.id;
											item.lastname = x.lastname;
											item.firstname = x.firstname;
											return item;
										}))
						);
	}
}
