import { Component, inject, OnInit } from '@angular/core';
import { SearchMovieService } from '../../../services/searchMovie.service';
import { MovieService } from '../../../services/movie.service';
import { from } from 'rxjs';
import { groupBy, mergeMap, toArray } from 'rxjs/operators';
import { StatsService } from '../../../services/stats.service';
import { PersonDetailedComponent } from 'src/app/entities/person-detailed/person-detailed.component';
import { PosterSimpleComponent } from 'src/app/entities/poster-simple/poster-simple.component';
import { AsyncPipe } from '@angular/common';

@Component({
    selector: 'app-stats-cinema',
    templateUrl: './stats-cinema.component.html',
    styleUrls: ['./stats-cinema.component.scss'],
	imports: [PersonDetailedComponent, PosterSimpleComponent, AsyncPipe]
})
export class StatsCinemaComponent implements OnInit {

	private searchMovieService = inject(SearchMovieService);
	private statsService = inject(StatsService);

	public moviesByYear = this.searchMovieService.getCinemaMovies();
	public actors: any[] = [];

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
