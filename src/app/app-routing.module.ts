import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { LastSeenComponent } from './last-seen/last-seen.component';
import { MoviesComponent } from './movies/movies.component';
import { StatsCinemaComponent } from './stats/cinema/stats-cinema.component';
import { StatsOscarsComponent } from './stats/oscars/stats-oscars.component';
import { OscarsComponent } from './oscars/oscars.component';
import { StatsActorsComponent } from './stats/actors/stats-actors.component';
import { StatsDirectorsComponent } from './stats/directors/stats-directors.component';
import { StatsSagasComponent } from './stats/sagas/stats-sagas.component';

const routes: Routes = [
  {
	path: '',
	component: LastSeenComponent,
    children: []
  },
  {
	path: 'movies',
	component: MoviesComponent,
    children: []
  },
  {
	path: 'oscars',
	component: OscarsComponent,
    children: []
  },
  {
	path: 'stats',
    children: [
		{
			path: 'cinema',
			component: StatsCinemaComponent
		},
		{
			path: 'sagas',
			component: StatsSagasComponent
		},
		{
			path: 'oscars',
			component: StatsOscarsComponent
		},
		{
			path: 'actors',
			component: StatsActorsComponent
		},
		{
			path: 'directors',
			component: StatsDirectorsComponent
		}
	]
  }
];

@NgModule({
  imports: [RouterModule.forRoot(routes, { relativeLinkResolution: 'legacy' })],
  exports: [RouterModule]
})
export class AppRoutingModule { }
