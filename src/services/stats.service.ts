import { HttpClient } from '@angular/common/http';

import { Injectable } from '@angular/core';
import { UrlBuilderService } from './url-builder.service';

import { Movie } from '../entities/movie';
import { Person } from '../entities/person';
import { Subject } from 'rxjs/internal/Subject';
import { Observable } from 'rxjs/internal/Observable';

@Injectable({
  providedIn: 'root'
})
export class StatsService {

	public searchedDirectors = new Subject<Person[]>();
	public searchedActors = new Subject<Person[]>();
	public results = new Subject<Movie[]>();

	constructor(
		private http: HttpClient,
		private urlBuilder: UrlBuilderService
	) {}

	public getCinemaActors(): Observable<any[]> {
		return this.http.get<any[]>(this.urlBuilder.buildUrl('stats/getCinemaActors'));
	}

	public getMostSeenActors(count: number): Observable<{count: number, movies: Movie[], person: Person}[]> {
		return this.http.get<{count: number, movies: Movie[], person: Person}[]>(this.urlBuilder.buildUrl('stats/getMostSeenActors', count));
	}

	public getBestActors(count: number): Observable<{count: number, movies: Movie[], person: Person}[]> {
		return this.http.get<{count: number, movies: Movie[], person: Person}[]>(this.urlBuilder.buildUrl('stats/getBestActors', count));
	}

	public getFirstRoles(count: number): Observable<{count: number, movies: Movie[], person: Person}[]> {
		return this.http.get<{count: number, movies: Movie[], person: Person}[]>(this.urlBuilder.buildUrl('stats/getFirstRoles', count));
	}

	public getMostSeenDirectors(count: number): Observable<{count: number, movies: Movie[], person: Person}[]> {
		return this.http.get<{count: number, movies: Movie[], person: Person}[]>(this.urlBuilder.buildUrl('stats/getMostSeenDirs', count));
	}

	public getBestDirectors(count: number): Observable<{count: number, movies: Movie[], person: Person}[]> {
		return this.http.get<{count: number, movies: Movie[], person: Person}[]>(this.urlBuilder.buildUrl('stats/getBestDirs', count));
	}

	public getActorsAges(): Observable<Person[]> {
		return this.http.get<Person[]>(this.urlBuilder.buildUrl('stats/getAges', 'acts'));
	}

	public getDirectorsAges(): Observable<Person[]> {
		return this.http.get<Person[]>(this.urlBuilder.buildUrl('stats/getAges', 'dirs'));
	}

	public getPassedActors(): Observable<Person[]> {
		return this.http.get<Person[]>(this.urlBuilder.buildUrl('stats/getPassed', 'acts'));
	}

	public getCollabsActors(): Observable<Person[]> {
		return this.http.get<Person[]>(this.urlBuilder.buildUrl('stats/getCollabs'));
	}

	public getCollabsDirectorActor(): Observable<Person[]> {
		return this.http.get<Person[]>(this.urlBuilder.buildUrl('stats/getCollabsDirs'));
	}
}
