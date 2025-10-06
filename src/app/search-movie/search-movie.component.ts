import { Component, OnInit } from '@angular/core';
import { SimpleEntity } from '../../entities/simple-entity';
import { Person } from '../../entities/person';
import { SearchMovieService } from '../../services/searchMovie.service';
import { debounceTime } from 'rxjs/operators';
import { ActivatedRoute } from '@angular/router';
import { SearchPersonService } from '../../services/searchPerson.service';
import { MultiSelectComponent } from '../multi-select/multi-select.component';
import { FormsModule } from '@angular/forms';

@Component({
    selector: 'app-search-movie',
    templateUrl: './search-movie.component.html',
    styleUrls: ['./search-movie.component.scss'],
	imports: [MultiSelectComponent, FormsModule]
})
export class SearchMovieComponent implements OnInit {

	public displayPeopleCallback: Function;

	public titleFr: string;
	public title: string;

	public years: string[];
	public yearBefore: string;
	public yearAfter: string;

	public markBefore: string;
	public markAfter: string;

	public owned: boolean;
	public cinema: boolean;

	public categories: SimpleEntity[];
	public categoriesSelected: SimpleEntity[];

	public countries: SimpleEntity[];
	public countriesSelected: SimpleEntity[];

	public directors: Person[];
	public directorsSelected: Person[];

	public actors: Person[];
	public actorsSelected: Person[];

	constructor(
		private searchMovieService: SearchMovieService,
		private route: ActivatedRoute,
		private searchPersonService: SearchPersonService
	) {}

	ngOnInit() {
		this.markBefore = "1";
		this.markAfter = "10";

		this.displayPeopleCallback = this.displayPeople.bind(this);

		this.searchMovieService.getActiveYears()
			.subscribe(years => {
				this.years = years;
				this.yearBefore = this.years[0];
				this.yearAfter = this.years[this.years.length - 1];
			});
		this.searchMovieService.getCategories()
			.subscribe(x => { this.categories = x; });
		this.searchMovieService.getCountries()
			.subscribe(x => { this.countries = x; });
		this.searchMovieService.getDirectors()
			.subscribe(x => { this.directors = x; });
		this.route.params.subscribe(params => {
			this.searchMovieService.getActors(params['acts'] ? params['acts'] : null)
				.subscribe(x => { this.actors = x; });
		});
	}

	public displayPeople(person: Person) {
		return person.firstname + ' ' + person.lastname;
	}

	public updatePeople(type: string) {
		if (type === "dir") {
			this.searchPersonService.getPeopleById(
				this.directorsSelected.map(p => p.id), 'dir'
			).subscribe();
		} else if (type === "act") {
			this.searchPersonService.getPeopleById(
				this.actorsSelected.map(p => p.id), 'act'
			).subscribe();
		}
	}

	public updateDirectors(selected) {
		this.directorsSelected = selected;
		this.updatePeople('dir');
		this.search();
	}

	public updateActors(selected) {
		this.actorsSelected = selected;
		this.updatePeople('act');
		this.search();
	}

	public search() {
		this.searchMovieService.search(
			this.titleFr,
			this.title,
			this.yearBefore,
			this.yearAfter,
			this.markBefore,
			this.markAfter,
			this.categoriesSelected,
			this.countriesSelected,
			this.directorsSelected,
			this.actorsSelected,
			this.owned,
			this.cinema)
				.pipe(debounceTime(500))
				.subscribe();
	}

}
