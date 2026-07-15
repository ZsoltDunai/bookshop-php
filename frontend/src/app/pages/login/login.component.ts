import { Component, inject } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { AuthService } from '../../services/auth.service';
import { CartService } from '../../services/cart.service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [RouterLink, FormsModule],
  template: `
    <section class="auth-card">
      <h1 data-testid="login-heading">Welcome back</h1>
      <p class="auth-sub">Sign in to browse, cart, and checkout.</p>

      @if (error) {
        <div class="alert alert-error" data-testid="login-alert">{{ error }}</div>
      }

      <form class="auth-form" (ngSubmit)="submit()">
        <label>
          Email
          <input type="email" [(ngModel)]="email" name="email" required autofocus data-testid="login-email" />
        </label>
        <label>
          Password
          <input type="password" [(ngModel)]="password" name="password" required data-testid="login-password" />
        </label>
        <button type="submit" class="btn btn-primary btn-block" data-testid="login-submit">Sign in</button>
      </form>

      <p class="auth-footer">
        Don't have an account? <a routerLink="/register">Create one</a>
      </p>

      <div class="demo-box">
        <strong>Demo account</strong>
        <p>demo&#64;bookshop.io / password123</p>
      </div>
    </section>
  `,
})
export class LoginComponent {
  private readonly auth = inject(AuthService);
  private readonly cart = inject(CartService);
  private readonly router = inject(Router);

  email = '';
  password = '';
  error = '';

  async submit(): Promise<void> {
    this.error = '';
    const message = await this.auth.login(this.email, this.password);
    if (message) {
      this.error = 'Invalid email or password.';
      return;
    }

    await this.cart.refresh();
    await this.router.navigate(['/']);
  }
}
