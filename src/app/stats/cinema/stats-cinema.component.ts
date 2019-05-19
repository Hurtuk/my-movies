import { Component, OnInit } from '@angular/core';
import { SearchMovieService } from '../../../services/searchMovie.service';
import { MovieService } from '../../../services/movie.service';
import { from } from 'rxjs';
import { groupBy, mergeMap, toArray } from 'rxjs/operators';
import { StatsService } from '../../../services/stats.service';

@Component({
  selector: 'app-stats-cinema',
  templateUrl: './stats-cinema.component.html',
  styleUrls: [ './stats-cinema.component.scss' ]
})
export class StatsCinemaComponent implements OnInit {

	public moviesByYear = this.searchMovieService.getCinemaMovies();
	public actors = [];

	constructor(
		private searchMovieService: SearchMovieService,
		private statsService: StatsService
	) {}

	ngOnInit() {
		this.statsService.getCinemaActors()
			.subscribe(people => {
				from(people).pipe(
					groupBy(x => x.movies.length),
					mergeMap(group => group.pipe(toArray()))
				)
				.subscribe(p => {
					this.actors.push(p);
				});
			});
	}

	public lengthToString(length: number): string {
		return MovieService.formatLength(length);
	}

}
