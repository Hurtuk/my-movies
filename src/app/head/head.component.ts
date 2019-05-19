import { Component, OnInit } from '@angular/core';
import { MovieService } from '../../services/movie.service';

@Component({
  selector: 'app-head',
  templateUrl: './head.component.html',
  styleUrls: [ './head.component.scss' ]
})
export class HeadComponent implements OnInit {

	public globalData = this.movieService.getGlobalInfo();

	constructor(
		private movieService: MovieService
	) { }

	ngOnInit() {
	}

}
