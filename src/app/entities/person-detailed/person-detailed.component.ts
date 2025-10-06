import { Component, Input, OnInit } from '@angular/core';
import { Person } from '../../../entities/person';
import { PersonSimpleComponent } from '../person-simple/person-simple.component';
import { RouterLink } from '@angular/router';

@Component({
    selector: 'app-person-detailed',
    templateUrl: './person-detailed.component.html',
    styleUrls: ['./person-detailed.component.scss'],
	imports: [PersonSimpleComponent, RouterLink]
})
export class PersonDetailedComponent implements OnInit {

	private UNCREDITED_LABEL = " (uncredited)";

	@Input() person: Person;
	@Input() width = 130;
	@Input() height = 160;
	public isUncredited: boolean;

	constructor() {}

	ngOnInit() {
		if (this.person.role) {
			// Uncredited
			const index = this.person.role.indexOf(this.UNCREDITED_LABEL);
			if (index !== -1) {
				this.isUncredited = true;
				this.person.role = this.person.role.substring(0, index);
			}
		}
	}

}
