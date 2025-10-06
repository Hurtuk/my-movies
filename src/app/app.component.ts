import { Component } from '@angular/core';
import { HeadComponent } from './head/head.component';
import { RouterOutlet } from '@angular/router';

@Component({
    selector: 'app-root',
    templateUrl: './app.component.html',
    styleUrls: ['./app.component.scss'],
    imports: [HeadComponent, RouterOutlet]
})
export class AppComponent {
	
}
