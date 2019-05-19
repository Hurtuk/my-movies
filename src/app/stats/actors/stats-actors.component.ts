import { Component } from '@angular/core';
import { map } from 'rxjs/operators';
import { StatsService } from '../../../services/stats.service';

@Component({
  selector: 'app-stats-actors',
  templateUrl: './stats-actors.component.html',
  styleUrls: [ './stats-actors.component.scss' ]
})
export class StatsActorsComponent {

	private MOSTSEEN_COUNT = 38;
	private FIRSTROLES_COUNT = 20;

	public mostSeenActors = this.statsService.getMostSeenActors(this.MOSTSEEN_COUNT).pipe(
		map((x: any[]) =>
		({
			widthFunction: w => 30 + 5 * (w - 15),
			heightFunction: h => 40 + 7 * (h - 15),
			fontSizeFunction: fs => .3 + .05 * (fs - 15),
			people: x
		}))
	);

	public bestActors = this.statsService.getBestActors(this.MOSTSEEN_COUNT).pipe(
		map((x: any[]) =>
		({
			widthFunction: w => 80 + 100 * (w - 8),
			heightFunction: h => 100 + 120 * (h - 8),
			fontSizeFunction: fs => .7 + .5 * (fs - 8),
			people: x
		}))
	);

	public firstRoles = this.statsService.getFirstRoles(this.FIRSTROLES_COUNT).pipe(
		map((x: any[]) =>
		({
			widthFunction: w => 50 + 4 * (w - 12),
			heightFunction: h => 65 + 5 * (h - 12),
			fontSizeFunction: fs => .7 + .04 * (fs - 12),
			people: x
		}))
	);

	constructor(
		private statsService: StatsService
	) {}

}
