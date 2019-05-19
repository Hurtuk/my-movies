import { Http } from '@angular/http';

import { Injectable } from '@angular/core';
import { UrlBuilderService } from './url-builder.service';

import { map } from 'rxjs/operators';
import { Movie } from '../entities/movie';
import { Person } from '../entities/person';
import { Subject } from 'rxjs/internal/Subject';
import { Observable } from 'rxjs/internal/Observable';

@Injectable()
export class StatsService {

	public searchedDirectors = new Subject<Person[]>();
	public searchedActors = new Subject<Person[]>();
	public results = new Subject<Movie[]>();

	constructor(
		private http: Http,
		private urlBuilder: UrlBuilderService
	) {}

	public getCinemaActors(): Observable<any[]> {
		return this.simpleGet<any[]>('getCinemaActors');
	}

	public getMostSeenActors(count: number): Observable<{count: number, movies: Movie[], person: Person}[]> {
		return this.simpleGet<{count: number, movies: Movie[], person: Person}[]>('getMostSeenActors', count);
	}

	public getBestActors(count: number): Observable<{count: number, movies: Movie[], person: Person}[]> {
		return this.simpleGet<{count: number, movies: Movie[], person: Person}[]>('getBestActors', count);
	}

	public getFirstRoles(count: number): Observable<{count: number, movies: Movie[], person: Person}[]> {
		return this.simpleGet<{count: number, movies: Movie[], person: Person}[]>('getFirstRoles', count);
	}

	public getMostSeenDirectors(count: number): Observable<{count: number, movies: Movie[], person: Person}[]> {
		return this.simpleGet<{count: number, movies: Movie[], person: Person}[]>('getMostSeenDirs', count);
	}

	public getBestDirectors(count: number): Observable<{count: number, movies: Movie[], person: Person}[]> {
		return this.simpleGet<{count: number, movies: Movie[], person: Person}[]>('getBestDirs', count);
	}

	public getActorsAges(): Observable<Person[]> {
		return this.simpleGet<Person[]>('getAges', 'acts');
	}

	public getDirectorsAges(): Observable<Person[]> {
		return this.simpleGet<Person[]>('getAges', 'dirs');
	}

	private simpleGet<T>(url: string, ...args: any[]): Observable<T> {
		return this.http.get(this.urlBuilder.buildUrl('stats/' + url, args))
				.pipe(
					map(response =>	response.json().data)
				);
	}
}
