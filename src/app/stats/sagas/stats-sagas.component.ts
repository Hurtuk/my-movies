import { Component, inject } from '@angular/core';
import { SearchMovieService } from '../../../services/searchMovie.service';
import { MovieService } from '../../../services/movie.service';
import { Movie } from '../../../entities/movie';
import { PosterSimpleComponent } from 'src/app/entities/poster-simple/poster-simple.component';
import { NgCircleProgressModule } from 'ng-circle-progress';
import { RouterLink } from '@angular/router';
import { PersonDetailedComponent } from 'src/app/entities/person-detailed/person-detailed.component';
import { AsyncPipe } from '@angular/common';

@Component({
    selector: 'app-stats-sagas',
    templateUrl: './stats-sagas.component.html',
    styleUrls: ['./stats-sagas.component.scss'],
	imports: [PosterSimpleComponent, PersonDetailedComponent, NgCircleProgressModule, RouterLink, AsyncPipe]
})
export class StatsSagasComponent {

	private searchMovieService = inject(SearchMovieService);
	private movieService = inject(MovieService);

	public sagas = this.searchMovieService.getSagas();
	public selectedSaga: number | null;
	public sagaDetails: Movie[];
	public hoveredMovie: Movie | null;

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
		return this.hoveredMovie!.actors.some(actor => actor.id === personId);
	}

	public lengthToString(length: number): string {
		return MovieService.formatLength(length);
	}

}
