import { Injectable, OnInit } from '@angular/core';
import { Http } from '@angular/http';

@Injectable()
export class UrlBuilderService {

    private urlPrefix = 'http://www.louiecinephile.fr/moviesServer/';
	
	public cacheToken = "";

	constructor(
		private http: Http
	) {
		this.http.get(this.buildUrl('getCacheToken'))
			.subscribe(x => {
				this.cacheToken = x.json().data
			});
	}

    public buildUrl(request: string, ...args: any[]): string {
		return this.urlPrefix + 'api/' + this.replaceContent(
			request + '.php?cache=' + this.cacheToken + '&' + args.map((arg, index) =>
								'arg' + index + (arg ? '={' + index + '}' : '=')).join('&'),
			args);
	}

    public genericUrl(relative: string): string {
        return this.urlPrefix + relative;
    }

    private replaceContent(source: string, args: any[]) {
        return source.replace(/{(\d+)}/g,
                            (match, number) =>
                                typeof args[number] !== 'undefined'
                                    ? '' + args[number]
                                    : match);
    }
}
