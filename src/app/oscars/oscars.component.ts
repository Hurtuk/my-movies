import { Component, OnInit } from '@angular/core';
import { OscarService } from '../../services/oscar.service';
import { OscarNomination } from '../../entities/oscarNomination';
import { Person } from '../../entities/person';
import { PosterSimpleComponent } from '../entities/poster-simple/poster-simple.component';
import { PersonSimpleComponent } from '../entities/person-simple/person-simple.component';
import { FormsModule } from '@angular/forms';

@Component({
    selector: 'app-oscars',
    templateUrl: './oscars.component.html',
    styleUrls: ['./oscars.component.scss'],
	imports: [PosterSimpleComponent, PersonSimpleComponent, FormsModule]
})
export class OscarsComponent implements OnInit {

	public BY_YEAR = 1;
	public BY_TYPE = 2;

	public currentType: number;

	public options: string[];
	public currentOption: string;

	public items: {
		key: string,
		winners: OscarNomination[],
		nominees: OscarNomination[],
		candidates: number
	}[];

	constructor(
		private oscarService: OscarService
	) {}

	ngOnInit() {
		
	}

	public changeType(newType: number) {
		this.currentType = newType;
		this.options = [];
		this.items = [];
		switch (this.currentType) {
			case this.BY_TYPE:
				this.oscarService.getTypes().subscribe(y => {
					this.options = y.map(o => this.cutPrefix(o));
				});
				break;
			case this.BY_YEAR:
				this.oscarService.getYears().subscribe(y => {
					this.options = y;
					this.currentOption = this.options[0];
					this.updateOscars();
				});
				break;
		}
	}

	public updateOscars() {
		this.currentOption;
		this.items = [];
		switch (this.currentType) {
			case this.BY_TYPE:
				this.oscarService.getOscarsByType(this.currentOption).subscribe(y => {
					this.items = y;
				});
				break;
			case this.BY_YEAR:
				this.oscarService.getOscarsByYear(this.currentOption).subscribe(y => {
					this.items = y;
				});
				break;
		}
	}

	public showPerson(item): boolean {
		return item.key.indexOf("Acteur") !== -1
			|| item.key.indexOf("Actrice") !== -1
			|| item.key.indexOf("Réalisateur") !== -1
			|| (
				this.currentType === this.BY_TYPE
				&& (this.currentOption.indexOf("Acteur") !== -1
					|| this.currentOption.indexOf("Actrice") !== -1
					|| this.currentOption.indexOf("Réalisateur") !== -1)
			)
	}

	public getUnknowns(item): number[] {
		return [].constructor(Math.max(0, item.candidates - item.nominees.length - Math.max(1, item.winners.length)));
	}

	public displayPeople(people: Person[]): string {
		return people.map(p => p.firstname + ' ' + p.lastname).join(', ');
	}

	private cutPrefix(str: string): string {
		return str.replace(/(Oscar du Meilleur )|(Oscar de la Meilleure )|(Oscar des Meilleurs )/g, '');
	}

}
