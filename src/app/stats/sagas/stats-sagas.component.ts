import { Component, OnInit } from '@angular/core';
import { SearchMovieService } from '../../../services/searchMovie.service';
import { MovieService } from '../../../services/movie.service';
import { Movie } from '../../../entities/movie';

@Component({
  selector: 'app-stats-sagas',
  templateUrl: './stats-sagas.component.html',
  styleUrls: [ './stats-sagas.component.scss' ]
})
export class StatsSagasComponent implements OnInit {

	public sagas = this.searchMovieService.getSagas();
	public selectedSaga: number;
	public sagaDetails: Movie[];
	public hoveredMovie: Movie;

	constructor(
		private searchMovieService: SearchMovieService,
		private movieService: MovieService
	) {}

	ngOnInit() {
	}

	public displayDetails(sagaId) {
		if (this.selectedSaga == sagaId) {
			this.selectedSaga = null;
		} else {
			this.sagaDetails = [];
			this.selectedSaga = sagaId;
			this.movieService.getSagaDetails(sagaId).subscribe(x => {
				this.sagaDetails = x;
			});
		}
	}

	public playedIn(personId: number): boolean {
		return this.hoveredMovie.actors.some(actor => actor.id === personId);
	}

	public lengthToString(length: number): string {
		return MovieService.formatLength(length);
	}

}
