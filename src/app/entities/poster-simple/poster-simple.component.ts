import { Component, Input, OnChanges } from '@angular/core';
import { VignetteService } from '../../../services/vignette.service';
import { Movie } from '../../../entities/movie';

@Component({
    selector: 'app-poster-simple',
    templateUrl: './poster-simple.component.html',
    styleUrls: ['./poster-simple.component.scss']
})
export class PosterSimpleComponent implements OnChanges {

	@Input() movie: Movie;
	@Input() icons = true;
	@Input() smallicons = false;
	public posterUrl: string;

	constructor(
		private vignetteService: VignetteService
	) {}

	ngOnChanges() {
		this.posterUrl = this.vignetteService.getPoster(this.movie.id);
	}

}
