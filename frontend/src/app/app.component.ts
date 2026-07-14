import { Component, OnInit, computed, inject } from '@angular/core';
import { RouterLink, RouterOutlet } from '@angular/router';
import { AuthService } from './services/auth.service';
import { CartService } from './services/cart.service';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet, RouterLink],
  template: `
    <header class="site-header">
      <div class="container header-inner">
        <a routerLink="/" class="logo">
          <span class="logo-icon">📚</span>
          <span class="logo-text">Bookshop</span>
        </a>

        <nav class="nav">
          <a routerLink="/" class="nav-link" data-testid="nav-browse">Browse</a>
          @if (auth.isLoggedIn) {
            <a routerLink="/cart" class="nav-link" data-testid="nav-cart">
              Cart
              @if (cartCount() > 0) {
                <span class="badge" data-testid="nav-cart-count">{{ cartCount() }}</span>
              }
            </a>
            <a routerLink="/orders" class="nav-link" data-testid="nav-orders">Orders</a>
            <span class="nav-user" data-testid="nav-user">{{ auth.user()?.email }}</span>
            <button type="button" class="btn btn-ghost btn-sm" data-testid="nav-logout" (click)="auth.logout()">
              Logout
            </button>
          } @else {
            <a routerLink="/login" class="nav-link" data-testid="nav-login">Login</a>
            <a routerLink="/register" class="btn btn-primary btn-sm" data-testid="nav-register">Sign up</a>
          }
        </nav>
      </div>
    </header>

    <main class="main">
      <div class="container">
        <router-outlet />
      </div>
    </main>

    <footer class="site-footer">
      <div class="container">
        <p>Bookshop Demo — PHP JSON API with Angular frontend</p>
        @if (!auth.isLoggedIn) {
          <p class="demo-hint">Demo account: <strong>demo&#64;bookshop.io</strong> / <strong>password123</strong></p>
        }
      </div>
    </footer>
  `,
})
export class AppComponent implements OnInit {
  readonly auth = inject(AuthService);
  readonly cart = inject(CartService);
  readonly cartCount = computed(() => this.cart.itemCount);

  ngOnInit(): void {
    void this.cart.refresh();
  }
}
