import { Http } from '@angular/http';

import { Injectable } from '@angular/core';
import { UrlBuilderService } from './url-builder.service';

import { map } from 'rxjs/operators';
import { Observable } from 'rxjs/internal/Observable';
import { MovieService } from './movie.service';

@Injectable()
export class OscarService {

	constructor(
		private http: Http,
		private urlBuilder: UrlBuilderService,
		private movieService: MovieService
	) {}

	public getYears(): Observable<string[]> {
		return this.http.get(this.urlBuilder.buildUrl('getOscarYears'))
						.pipe(map(
							response => response.json().data
						));
	}

	public getTypes(): Observable<string[]> {
		return this.http.get(this.urlBuilder.buildUrl('getOscarTypes'))
						.pipe(map(
							response => response.json().data
						));
	}

	public getOscarsByType(type: string): Observable<any[]> {
		return this.http.get(this.urlBuilder.buildUrl('getOscarsByType', type))
						.pipe(map(
							response => response.json().data.map(o => ({
								key: o.year,
								candidates: o.candidates,
								winners: o.winners.map(w =>
									this.movieService.jsonToOscars({
										nominees: w.nominees.map(p => {
											const data = p.split('_');
											return { id: data[0], firstname: data[1], lastname: data[2] };
										}),
										detail: w.oscarDetail,
										movie: {
											id: w.id,
											titleFr: w.titleFr
										}
									})
								),
								nominees: o.nominees.map(w =>
									this.movieService.jsonToOscars({
										nominees: w.nominees.map(p => {
											const data = p.split('_');
											return { id: data[0], firstname: data[1], lastname: data[2] };
										}),
										detail: w.oscarDetail,
										movie: {
											id: w.id,
											titleFr: w.titleFr
										}
									})
								)
							})))
						);
	}

	public getOscarsByYear(year: string): Observable<any[]> {
		return this.http.get(this.urlBuilder.buildUrl('getOscarsByYear', year))
						.pipe(map(
							response => response.json().data.map(o => ({
								key: o.name,
								candidates: o.candidates,
								winners: o.winners.map(w =>
									this.movieService.jsonToOscars({
										nominees: w.nominees.map(p => {
											const data = p.split('_');
											return { id: data[0], firstname: data[1], lastname: data[2] };
										}),
										detail: w.oscarDetail,
										movie: {
											id: w.id,
											titleFr: w.titleFr
										}
									})
								),
								nominees: o.nominees.map(w =>
									this.movieService.jsonToOscars({
										nominees: w.nominees.map(p => {
											const data = p.split('_');
											return { id: data[0], firstname: data[1], lastname: data[2] };
										}),
										detail: w.oscarDetail,
										movie: {
											id: w.id,
											titleFr: w.titleFr
										}
									})
								)
							})))
						);
	}
}
