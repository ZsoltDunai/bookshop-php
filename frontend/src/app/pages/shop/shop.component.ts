import { UpperCasePipe } from '@angular/common';
import { Component, OnInit, inject } from '@angular/core';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { Book } from '../../models';
import { BookService } from '../../services/book.service';
import { CartService } from '../../services/cart.service';
import { AuthService } from '../../services/auth.service';
import { ApiService } from '../../services/api.service';
import { PricePipe } from '../../pipes/price.pipe';

@Component({
  selector: 'app-shop',
  standalone: true,
  imports: [RouterLink, FormsModule, UpperCasePipe, PricePipe],
  template: `
    <section class="hero">
      <h1 data-testid="home-heading">Discover your next great read</h1>
      <p class="hero-sub">Curated classics and modern favorites, delivered to your door.</p>

      <form class="search-form" (ngSubmit)="search()">
        <input
          type="search"
          class="search-input"
          placeholder="Search by title or author…"
          [(ngModel)]="query"
          name="q"
          data-testid="search-input"
        />
        <button type="submit" class="btn btn-primary" data-testid="search-submit">Search</button>
      </form>
    </section>

    @if (error) {
      <div class="alert alert-error">{{ error }}</div>
    }

    @if (books.length === 0) {
      <div class="empty-state" data-testid="search-empty">
        <p>No books found{{ query ? ' for "' + query + '"' : '' }}.</p>
        @if (query) {
          <a routerLink="/" class="btn btn-ghost">Clear search</a>
        }
      </div>
    } @else {
      <div class="book-grid" data-testid="book-grid">
        @for (book of books; track book.id) {
          <article class="book-card" data-testid="book-card" [attr.data-book-id]="book.id">
            <div class="book-cover">
              <span class="book-cover-letter">{{ book.title[0] | uppercase }}</span>
            </div>
            <div class="book-info">
              <h2 class="book-title">
                <a [routerLink]="['/book', book.id]">{{ book.title }}</a>
              </h2>
              <p class="book-author">{{ book.author }}</p>
              <div class="book-meta">
                <span class="book-price">{{ book.price | price }}</span>
                <span class="book-stock" [class.low]="book.stock < 3">{{ book.stock }} in stock</span>
              </div>
              @if (auth.isLoggedIn && book.stock > 0) {
                <div class="add-form">
                  <button
                    type="button"
                    class="btn btn-secondary btn-block"
                    data-testid="add-to-cart"
                    (click)="addToCart(book.id)"
                  >
                    Add to cart
                  </button>
                </div>
              } @else if (!auth.isLoggedIn) {
                <a routerLink="/login" class="btn btn-ghost btn-block" data-testid="login-to-buy">Login to buy</a>
              } @else {
                <button class="btn btn-ghost btn-block" disabled>Out of stock</button>
              }
            </div>
          </article>
        }
      </div>
    }
  `,
})
export class ShopComponent implements OnInit {
  private readonly bookService = inject(BookService);
  private readonly cartService = inject(CartService);
  private readonly route = inject(ActivatedRoute);
  private readonly router = inject(Router);
  readonly auth = inject(AuthService);

  books: Book[] = [];
  query = '';
  error = '';

  ngOnInit(): void {
    this.route.queryParamMap.subscribe((params) => {
      this.query = params.get('q') ?? '';
      void this.loadBooks();
    });
  }

  async loadBooks(): Promise<void> {
    this.error = '';
    try {
      this.books = await this.bookService.list(this.query);
    } catch (error) {
      this.error = ApiService.errorMessage(error);
      this.books = [];
    }
  }

  search(): void {
    const q = this.query.trim();
    void this.router.navigate(['/'], { queryParams: q ? { q } : {} });
  }

  async addToCart(bookId: number): Promise<void> {
    const message = await this.cartService.add(bookId, 1);
    if (message) {
      this.error = message;
    }
  }
}
