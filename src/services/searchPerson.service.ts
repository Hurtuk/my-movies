import { HttpClient } from '@angular/common/http';

import { Injectable } from '@angular/core';
import { UrlBuilderService } from './url-builder.service';

import { map } from 'rxjs/operators';
import { Movie } from '../entities/movie';
import { Person } from '../entities/person';
import { MovieService } from './movie.service';
import { Subject } from 'rxjs/internal/Subject';
import { Observable } from 'rxjs/internal/Observable';
import { forkJoin, of } from 'rxjs';

@Injectable()
export class SearchPersonService {

	public searchedDirectors = new Subject<Person[]>();
	public searchedActors = new Subject<Person[]>();
	public results = new Subject<Movie[]>();

	constructor(
		private http: HttpClient,
		private urlBuilder: UrlBuilderService,
		private movieService: MovieService
	) {}

	// TODO
	public search(): Observable<any> {
		return this.http.get(this.urlBuilder.buildUrl(
			'searchPeople',
			/*titleFr,
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
			cinema ? "1" : "" */))
					/*.pipe(
						map(response => {
							this.results.next(
								(response.json().data as any[])
									.map(m => this.movieService.jsonToMovie(m)));
							})
					)*/;
	}

	public getPeopleById(ids: number[], type: string): Observable<Person[]> {
		if (!ids.length) {
			if (type === 'act') {
				this.searchedActors.next([]);
			} else if (type === 'dir') {
				this.searchedDirectors.next([]);
			}
			return of([]);
		}
		const observables: Observable<Person>[] = [];
		ids.forEach(id => observables.push(this.getPersonById(id, type)));
		return forkJoin(observables)
				.pipe(map(people => {
					if (type === 'act') {
						this.searchedActors.next(people);
					} else if (type === 'dir') {
						this.searchedDirectors.next(people);
					}
					return people;
				}));
	}

	public getPersonById(id: number, type: string): Observable<Person> {
		return this.http.get<any>(this.urlBuilder.buildUrl('getPerson', id))
							.pipe(
								map(response => this.movieService.jsonToPerson(response))
							);
	}

    /*public getDirectors(): Observable<Person[]> {
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
	}*/
}
