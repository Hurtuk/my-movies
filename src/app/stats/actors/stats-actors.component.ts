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

	public year = (new Date()).getFullYear();

	public mostSeenActors = this.statsService.getMostSeenActors(this.MOSTSEEN_COUNT).pipe(
		map((x: any[]) =>
		({
			widthFunction: w => 50 + 5 * (w - 20),
			heightFunction: h => 65 + 7 * (h - 20),
			fontSizeFunction: fs => .6 + .05 * (fs - 20),
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

	public ages = this.statsService.getActorsAges().pipe(
		map((x: any) =>
		({
			mold: {
				widthFunction: () => 100,
				heightFunction: () => 130,
				fontSizeFunction: () => 0,
				people: x.mold.map(m => ({person: m}))
			},
			fold: {
				widthFunction: () => 100,
				heightFunction: () => 130,
				fontSizeFunction: () => 0,
				people: x.fold.map(m => ({person: m}))
			},
			myoung: {
				widthFunction: () => 100,
				heightFunction: () => 130,
				fontSizeFunction: () => 0,
				people: x.myoung.map(m => ({person: m}))
			},
			fyoung: {
				widthFunction: () => 100,
				heightFunction: () => 130,
				fontSizeFunction: () => 0,
				people: x.fyoung.map(m => ({person: m}))
			}
		})));

	public passed = this.statsService.getPassedActors().pipe(
		map((x: any[]) =>
		({
			widthFunction: () => 75,
			heightFunction: () => 100,
			fontSizeFunction: () => 0,
			people: x.map(m => ({person: m}))
		}))
	);

	public collabs = this.statsService.getCollabsActors();

	constructor(
		private statsService: StatsService
	) {}

	public getAge(a: any): string | number {
		return this.year - a.born;
	}

}
