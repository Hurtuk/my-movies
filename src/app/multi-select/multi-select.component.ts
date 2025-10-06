import { Component, OnInit, Output, EventEmitter, Input } from '@angular/core';
import { SimpleEntity } from '../../entities/simple-entity';
import { ActivatedRoute } from '@angular/router';

@Component({
    selector: 'app-multi-select',
    templateUrl: './multi-select.component.html',
    styleUrls: ['./multi-select.component.scss']
})
export class MultiSelectComponent implements OnInit {

	@Input() urlParam: string;
	@Input() items: any[];
	@Input() displayFunction: Function;
	public selectedItems: any[] = [];

	public optionsOpened: boolean;

	@Output() change = new EventEmitter<any[]>();

	constructor(
		private route: ActivatedRoute
	) {}

	ngOnInit() {
		if (!this.displayFunction) {
			this.displayFunction = (x: SimpleEntity) => x.name;
		}
		this.route.params.subscribe(params => {
			if (params[this.urlParam]) {
			  	this.selectedItems =
					params[this.urlParam].split(',')
						.map(x => this.items.find(p => p.id == x));
				this.change.emit(this.selectedItems);
			}
		});
	}

	public remove(item: any) {
		this.selectedItems.splice(this.selectedItems.indexOf(item), 1);
		this.change.emit(this.selectedItems);
	}

	public add(item: any) {
		this.selectedItems.push(item);
		this.selectedItems.sort(
			(a, b) => 
				this.displayFunction(a) < this.displayFunction(b) ? -1 : this.displayFunction(a) > this.displayFunction(b) ? 1 : 0);
		this.optionsOpened = false;
		this.change.emit(this.selectedItems);
	}

}
