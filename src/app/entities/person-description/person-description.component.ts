import { Component, Input } from '@angular/core';
import { PersonSimpleComponent } from '../person-simple/person-simple.component';

@Component({
    selector: 'app-person-description',
    templateUrl: './person-description.component.html',
    styleUrls: ['./person-description.component.scss'],
    imports: [PersonSimpleComponent]
})
export class PersonDescriptionComponent {

	@Input() person: any;

}
