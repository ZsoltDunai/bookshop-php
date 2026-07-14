import { Component, inject } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [RouterLink, FormsModule],
  template: `
    <section class="auth-card">
      <h1 data-testid="register-heading">Create account</h1>
      <p class="auth-sub">Join Bookshop and start building your library.</p>

      @if (error) {
        <div class="alert alert-error" data-testid="register-alert">{{ error }}</div>
      }

      <form class="auth-form" (ngSubmit)="submit()">
        <label>
          Email
          <input type="email" [(ngModel)]="email" name="email" required autofocus data-testid="register-email" />
        </label>
        <label>
          Password
          <input type="password" [(ngModel)]="password" name="password" minlength="6" required data-testid="register-password" />
        </label>
        <button type="submit" class="btn btn-primary btn-block" data-testid="register-submit">Create account</button>
      </form>

      <p class="auth-footer">
        Already have an account? <a routerLink="/login">Sign in</a>
      </p>
    </section>
  `,
})
export class RegisterComponent {
  private readonly auth = inject(AuthService);
  private readonly router = inject(Router);

  email = '';
  password = '';
  error = '';

  async submit(): Promise<void> {
    this.error = '';
    const message = await this.auth.register(this.email, this.password);
    if (message) {
      this.error = message;
      return;
    }

    await this.router.navigate(['/']);
  }
}
