import { Component } from '@angular/core';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'app-not-found',
  standalone: true,
  imports: [RouterLink],
  template: `
    <div class="empty-state">
      <h1>404</h1>
      <p>Page not found.</p>
      <a routerLink="/" class="btn btn-primary">Back to browse</a>
    </div>
  `,
})
export class NotFoundComponent {}
