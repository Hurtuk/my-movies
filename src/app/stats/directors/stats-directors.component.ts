import { Component, inject } from '@angular/core';
import { StatsService } from '../../../services/stats.service';
import { map } from 'rxjs/operators';
import { PeopleListComponent } from '../people-list/people-list.component';
import { AsyncPipe } from '@angular/common';
import { Observable } from 'rxjs';
import { PersonSimpleComponent } from '@app/entities/person-simple/person-simple.component';
import { RouterLink } from '@angular/router';

@Component({
    selector: 'app-stats-directors',
    templateUrl: './stats-directors.component.html',
    styleUrls: ['./stats-directors.component.scss'],
	imports: [PeopleListComponent, AsyncPipe, PersonSimpleComponent, RouterLink]
})
export class StatsDirectorsComponent {

	private statsService = inject(StatsService);

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
	
	public collabs = this.statsService.getCollabsDirectorActor() as Observable<any[]>;

}
