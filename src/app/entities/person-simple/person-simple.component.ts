import { Component, Input, OnChanges } from '@angular/core';
import { VignetteService } from '../../../services/vignette.service';

@Component({
  selector: 'app-person-simple',
  templateUrl: './person-simple.component.html',
  styleUrls: [ './person-simple.component.scss' ]
})
export class PersonSimpleComponent implements OnChanges {

	@Input() personId: number;
	public photoUrl: string;

	constructor(
		private vignetteService: VignetteService
	) { }

	ngOnChanges() {
		this.photoUrl = this.vignetteService.getPersonPhoto(this.personId);
	}

}
