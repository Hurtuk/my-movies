import { Component, Input } from '@angular/core';
import { OscarNomination } from '../../entities/oscarNomination';

@Component({
  selector: 'app-oscar-box',
  templateUrl: './oscar-box.component.html',
  styleUrls: [ './oscar-box.component.scss' ]
})
export class OscarBoxComponent {

	@Input() oscars: OscarNomination[];

	public getOscarsWon(): OscarNomination[] {
		return this.oscars.filter(o => o.won);
	}

	public getOscarsNominations(): OscarNomination[] {
		return this.oscars.filter(o => !o.won);
	}

}
