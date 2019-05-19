import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-person-panel',
  templateUrl: './person-panel.component.html',
  styleUrls: [ './person-panel.component.scss' ]
})
export class PersonPanelComponent {

	@Input() person: any;

	public getAge(): string {
		if (!this.person.born) {
			return '';
		}
		if (this.person.died) {
			return '(† ' + (this.person.died - this.person.born) + ' ans)';
		}
		return '(' + ((new Date()).getFullYear() - this.person.born) + ' ans)';
	}

}
