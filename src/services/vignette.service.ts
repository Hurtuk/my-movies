import { Injectable } from '@angular/core';
import { UrlBuilderService } from './url-builder.service';

@Injectable()
export class VignetteService {

	constructor(
		private urlBuilder: UrlBuilderService
	) {}

    public getPersonPhoto(id: number): string {
		return this.urlBuilder.genericUrl('images/people/' + id + '.jpg');
	}
	
    public getPoster(id: number): string {
		return this.urlBuilder.genericUrl('images/posters/' + id + '.jpg');
    }
	
    public getCategoryImage(id: number): string {
		return this.urlBuilder.genericUrl('images/categories/' + id + '.jpg');
	}
	
	public getFlagImage(country: string): string {
		return this.urlBuilder.genericUrl('images/countries/' + country + '.png');
	}
}
