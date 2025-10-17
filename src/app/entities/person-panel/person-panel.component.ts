import { Component, Input } from '@angular/core';
import { PersonDescriptionComponent } from '../person-description/person-description.component';
import { FlagComponent } from '../flag/flag.component';

@Component({
    selector: 'app-person-panel',
    templateUrl: './person-panel.component.html',
    styleUrls: ['./person-panel.component.scss'],
	imports: [PersonDescriptionComponent, FlagComponent]
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

	public formatOscar(oscar: string) {
		return oscar
			.replace("Oscar du ", "")
			.replace("Oscar de la ", "")
			.replace("Oscar des ", "");
	}

}
