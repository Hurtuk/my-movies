import { Component, Input } from '@angular/core';
import { Person } from '../../../entities/person';

@Component({
  selector: 'app-people-list',
  templateUrl: './people-list.component.html',
  styleUrls: [ './people-list.component.scss' ]
})
export class PeopleListComponent {

	@Input() type: string;
	@Input() data: {
		widthFunction: Function,
		heightFunction: Function,
		fontSizeFunction: Function,
		people: {
			count: string,
			movies: string[],
			person: Person
		}[]
	};

	constructor() {}

	public getPersonUrl(id: number) {
		const url = new Object();
		url[this.type] = id;
		return url;
	}

}
