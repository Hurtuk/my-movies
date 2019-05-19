import { Component, Input, OnInit } from '@angular/core';
import { Movie } from '../../../entities/movie';

@Component({
  selector: 'app-poster-detailed',
  templateUrl: './poster-detailed.component.html',
  styleUrls: [ './poster-detailed.component.scss' ]
})
export class PosterDetailedComponent implements OnInit {

	@Input() movie: Movie;

	constructor() {}

	ngOnInit() {
		
	}

}
