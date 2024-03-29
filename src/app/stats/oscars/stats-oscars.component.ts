import { Component, OnInit } from '@angular/core';
import { SearchMovieService } from '../../../services/searchMovie.service';
import { MovieService } from '../../../services/movie.service';
import { SearchPersonService } from '../../../services/searchPerson.service';
import { from } from 'rxjs';
import { groupBy, mergeMap, toArray } from 'rxjs/operators';

@Component({
  selector: 'app-stats-oscars',
  templateUrl: './stats-oscars.component.html',
  styleUrls: [ './stats-oscars.component.scss' ]
})
export class StatsOscarsComponent implements OnInit {

	public moviesByYear = this.searchMovieService.getCinemaMovies();
	public actors = [];

	constructor(
		private searchMovieService: SearchMovieService,
		private searchPersonService: SearchPersonService
	) {}

	ngOnInit() {
		/*this.searchPersonService.getCinemaActors()
			.subscribe(people => {
				from(people).pipe(
					groupBy(x => x.movies.length),
					mergeMap(group => group.pipe(toArray()))
				)
				.subscribe(p => {
					this.actors.push(p);
				});
			});*/
	}

	public lengthToString(length: number): string {
		return MovieService.formatLength(length);
	}

}
