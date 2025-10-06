import { Component, inject } from '@angular/core';
import { MovieService } from '../../services/movie.service';
import { SidebarComponent } from '../sidebar/sidebar.component';
import { AsyncPipe } from '@angular/common';
import { RouterLink } from '@angular/router';

@Component({
    selector: 'app-head',
    templateUrl: './head.component.html',
    styleUrls: ['./head.component.scss'],
	imports: [SidebarComponent, AsyncPipe, RouterLink]
})
export class HeadComponent {
	private movieService = inject(MovieService);

	public globalData = this.movieService.getGlobalInfo();

}
