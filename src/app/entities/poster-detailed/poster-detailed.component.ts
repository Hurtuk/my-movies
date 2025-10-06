import { Component, Input, OnInit } from '@angular/core';
import { Movie } from '../../../entities/movie';
import { PosterSimpleComponent } from '../poster-simple/poster-simple.component';

@Component({
    selector: 'app-poster-detailed',
    templateUrl: './poster-detailed.component.html',
    styleUrls: ['./poster-detailed.component.scss'],
	imports: [PosterSimpleComponent]
})
export class PosterDetailedComponent implements OnInit {

	@Input() movie: Movie;

	constructor() {}

	ngOnInit() {
		
	}

}
