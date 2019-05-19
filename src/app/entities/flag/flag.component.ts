import { Component, Input, OnInit } from '@angular/core';
import { VignetteService } from '../../../services/vignette.service';

@Component({
  selector: 'app-flag',
  templateUrl: './flag.component.html',
  styleUrls: [ './flag.component.scss' ]
})
export class FlagComponent implements OnInit {

	@Input() country: string;
	public flagUrl: string;

	constructor(
		private vignetteService: VignetteService
	) { }

	ngOnInit() {
		this.flagUrl = this.vignetteService.getFlagImage(this.country);
	}

}
