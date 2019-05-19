import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-person-description',
  templateUrl: './person-description.component.html',
  styleUrls: [ './person-description.component.scss' ]
})
export class PersonDescriptionComponent {

	@Input() person: any;

}
