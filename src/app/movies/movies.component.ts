import { Component, OnInit } from '@angular/core';
import { Movie } from '../../entities/movie';
import { SearchMovieService } from '../../services/searchMovie.service';
import { MovieService } from '../../services/movie.service';
import { SearchPersonService } from '../../services/searchPerson.service';
import { PersonPanelComponent } from '../entities/person-panel/person-panel.component';
import { PosterSimpleComponent } from '../entities/poster-simple/poster-simple.component';
import { SearchMovieComponent } from '../search-movie/search-movie.component';

@Component({
    selector: 'app-movies',
    templateUrl: './movies.component.html',
    styleUrls: ['./movies.component.scss'],
	imports: [PersonPanelComponent, PosterSimpleComponent, SearchMovieComponent]
})
export class MoviesComponent implements OnInit {

	public movies: Movie[] = [];
	public directors: any[];
	public actors: any[];

	constructor(
		private searchMovieService: SearchMovieService,
		private searchPersonService: SearchPersonService
	) {}

	ngOnInit() {
		this.searchMovieService.results.subscribe(m => {
			this.movies = m;
		});
		this.searchPersonService.searchedDirectors.subscribe(people => {
			people.forEach(p => {
				if (p.oscars) {
					p.oscars = this.orderOscars(p.oscars);
				}
			});
			this.directors = people;
		});
		this.searchPersonService.searchedActors.subscribe(people => {
			people.forEach(p => {
				if (p.oscars) {
					p.oscars = this.orderOscars(p.oscars);
				}
			});
			this.actors = people;
		});
	}

	public getAverageLength(): string {
		return MovieService.formatLength(this.movies
			.map(m => m.length)
			.reduce((prev, cur) => prev! += cur ?? 0, 0)! / this.movies.length);
	}

	public getAverageMark(): string {
		return (this.movies
			.map(m => m.mark)
			.reduce((prev, cur) => prev! += cur ?? 0, 0)! / this.movies.length).toFixed(2);
	}

	private orderOscars(oscars: any[]): any[] {
		const oscarsTemp: any[] = [];
		let prevYear: string = "";
		let prevMovieId: number = -1;
		oscars.forEach(o => {
			if (o.year !== prevYear) {
				oscarsTemp[o.year] = [];
			}
			if (o.year !== prevYear || o.movie.id !== prevMovieId) {
				oscarsTemp[o.year].push({
					movie: o.movie.titleFr,
					oscars: []
				});
			}
			oscarsTemp[o.year][oscarsTemp[o.year].length - 1].oscars.push(o);
		});
		return oscarsTemp;
	}

}
