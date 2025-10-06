import { Component, inject, OnInit } from '@angular/core';
import { MovieService } from '../../services/movie.service';
import { Movie } from '../../entities/movie';
import { VignetteService } from '../../services/vignette.service';
import { OscarNomination } from '../../entities/oscarNomination';
import { PosterSimpleComponent } from '../entities/poster-simple/poster-simple.component';
import { PersonDetailedComponent } from '../entities/person-detailed/person-detailed.component';
import { AsyncPipe } from '@angular/common';
import { PosterDetailedComponent } from '../entities/poster-detailed/poster-detailed.component';
import { RouterLink } from '@angular/router';

@Component({
    selector: 'app-last-seen',
    templateUrl: './last-seen.component.html',
    styleUrls: ['./last-seen.component.scss'],
	imports: [PosterSimpleComponent, PersonDetailedComponent, AsyncPipe, PosterDetailedComponent, RouterLink]
})
export class LastSeenComponent {

	private movieService = inject(MovieService);
	private vignetteService = inject(VignetteService);

	public latestMovies = this.movieService.getLatestMovies();
	public selected = 0;
	public oscarWon = 0;
	public oscarNominee = 0;

	public lengthToString(length: number): string {
		return MovieService.formatLength(length);
	}

	public getOscarsWon(oscars: OscarNomination[]): OscarNomination[] {
		const result = oscars.filter(o => o.won);
		this.oscarWon = result.length;
		return result;
	}

	public getOscarsNominations(oscars: OscarNomination[]): OscarNomination[] {
		const result = oscars.filter(o => !o.won);
		this.oscarNominee = result.length;
		return result;
	}

	public getBackground(movie: Movie) {
		return this.vignetteService.getCategoryImage(movie.categories[0].id);
	}

}
