import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { HeadComponent } from './head/head.component';
import { PersonSimpleComponent } from './entities/person-simple/person-simple.component';
import { LastSeenComponent } from './last-seen/last-seen.component';
import { PosterSimpleComponent } from './entities/poster-simple/poster-simple.component';
import { UrlBuilderService } from '../services/url-builder.service';
import { MovieService } from '../services/movie.service';
import { HttpModule } from '@angular/http';
import { VignetteService } from '../services/vignette.service';
import { PosterDetailedComponent } from './entities/poster-detailed/poster-detailed.component';
import { PersonDetailedComponent } from './entities/person-detailed/person-detailed.component';
import { SidebarComponent } from './sidebar/sidebar.component';
import { OscarBoxComponent } from './oscars-box/oscar-box.component';
import { MoviesComponent } from './movies/movies.component';
import { SearchMovieComponent } from './search-movie/search-movie.component';
import { FormsModule } from '@angular/forms';
import { SearchMovieService } from '../services/searchMovie.service';
import { MultiSelectComponent } from './multi-select/multi-select.component';
import { SearchPersonService } from '../services/searchPerson.service';
import { PersonDescriptionComponent } from './entities/person-description/person-description.component';
import { StatsCinemaComponent } from './stats/cinema/stats-cinema.component';
import { OscarsComponent } from './oscars/oscars.component';
import { StatsOscarsComponent } from './stats/oscars/stats-oscars.component';
import { OscarService } from '../services/oscar.service';
import { StatsActorsComponent } from './stats/actors/stats-actors.component';
import { PeopleListComponent } from './stats/people-list/people-list.component';
import { StatsDirectorsComponent } from './stats/directors/stats-directors.component';
import { PersonPanelComponent } from './entities/person-panel/person-panel.component';
import { FlagComponent } from './entities/flag/flag.component';
import { StatsSagasComponent } from './stats/sagas/stats-sagas.component';
import { NgCircleProgressModule } from 'ng-circle-progress';
import { StatsService } from '../services/stats.service';

@NgModule({
  declarations: [
    AppComponent,
    HeadComponent,
	PersonSimpleComponent,
	LastSeenComponent,
	PosterSimpleComponent,
	PosterDetailedComponent,
	PersonDetailedComponent,
	SidebarComponent,
	OscarBoxComponent,
	MoviesComponent,
	SearchMovieComponent,
	MultiSelectComponent,
	PersonDescriptionComponent,
	StatsCinemaComponent,
	OscarsComponent,
	StatsOscarsComponent,
	StatsActorsComponent,
	PeopleListComponent,
	StatsDirectorsComponent,
	PersonPanelComponent,
	FlagComponent,
	StatsSagasComponent
  ],
  imports: [
    BrowserModule,
	AppRoutingModule,
	HttpModule,
	FormsModule,
	NgCircleProgressModule.forRoot({
		radius: 15,
		titleFontSize: ".55em",
		outerStrokeWidth: 5,
		innerStrokeWidth: 5,
		space: -5,
		backgroundPadding: -1,
		backgroundColor: "white",
		outerStrokeColor: "#78C000",
		innerStrokeColor: "#e7e8ea",
		animation: false,
		showSubtitle: false
	})
  ],
  providers: [
	  UrlBuilderService,
	  MovieService,
	  VignetteService,
	  SearchMovieService,
	  SearchPersonService,
	  OscarService,
	  StatsService
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
