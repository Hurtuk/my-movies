import { Component } from '@angular/core';
import { StatsService } from '../../../services/stats.service';
import { map } from 'rxjs/operators';

@Component({
  selector: 'app-stats-directors',
  templateUrl: './stats-directors.component.html',
  styleUrls: [ './stats-directors.component.scss' ]
})
export class StatsDirectorsComponent {

	private MOSTSEEN_COUNT = 38;
	private BEST_COUNT = 30;

	public mostSeenDirectors = this.statsService.getMostSeenDirectors(this.MOSTSEEN_COUNT).pipe(
		map((x: any[]) =>
		({
			widthFunction: w => 60 + 2 * (w - 10),
			heightFunction: h => 75 + 3 * (h - 10),
			fontSizeFunction: fs => .7 + .02 * (fs - 10),
			people: x
		}))
	);

	public bestDirectors = this.statsService.getBestDirectors(this.BEST_COUNT).pipe(
		map((x: any[]) =>
		({
			widthFunction: w => 55 + 50 * (w - 7.5),
			heightFunction: h => 70 + 70 * (h - 7.5),
			fontSizeFunction: fs => .7 + .5 * (fs - 7.5),
			people: x
		}))
	);

	constructor(
		private statsService: StatsService
	) {}

}
