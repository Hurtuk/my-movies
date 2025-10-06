import { enableProdMode, importProvidersFrom } from '@angular/core';
import { bootstrapApplication } from '@angular/platform-browser';
import { AppComponent } from './app/app.component';
import { environment } from './environments/environment';

// Si tu as encore besoin de modules globaux
import { BrowserModule } from '@angular/platform-browser';
import { provideHttpClient, withInterceptorsFromDi } from '@angular/common/http';
import { provideRouter } from '@angular/router';
import { routes } from './app/routes';
import { NgCircleProgressModule } from 'ng-circle-progress';

if (environment.production) {
  enableProdMode();
}

bootstrapApplication(AppComponent, {
  providers: [
    importProvidersFrom(BrowserModule),
    provideHttpClient(withInterceptorsFromDi()),
    provideRouter(routes),
    importProvidersFrom(NgCircleProgressModule.forRoot({
      radius: 15,
      titleFontSize: ".55em",
      outerStrokeWidth: 5,
      innerStrokeWidth: 5,
      space: -5,
      backgroundPadding: -1,
      backgroundColor: "white",
      outerStrokeColor: "#78C000",
      innerStrokeColor: "#e7e8ea",
      animation: false,
      showSubtitle: false
    }))
  ],
}).catch(err => console.error(err));
