import { Component, OnInit } from '@angular/core';
import { MovieService } from '../../services/movie.service';
import { Movie } from '../../entities/movie';
import { VignetteService } from '../../services/vignette.service';
import { OscarNomination } from '../../entities/oscarNomination';

@Component({
  selector: 'app-last-seen',
  templateUrl: './last-seen.component.html',
  styleUrls: [ './last-seen.component.scss' ]
})
export class LastSeenComponent implements OnInit {

	public latestMovies = this.movieService.getLatestMovies();
	public selected = 0;
	public oscarWon = 0;
	public oscarNominee = 0;

	constructor(
		private movieService: MovieService,
		private vignetteService: VignetteService
	) {}

	ngOnInit() {
	}

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
