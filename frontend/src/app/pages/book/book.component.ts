import { UpperCasePipe } from '@angular/common';
import { Component, OnInit, inject } from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { Book } from '../../models';
import { BookService } from '../../services/book.service';
import { CartService } from '../../services/cart.service';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-book',
  standalone: true,
  imports: [RouterLink, FormsModule, UpperCasePipe],
  template: `
    @if (notFound) {
      <div class="empty-state">
        <h1>404</h1>
        <p>Book not found.</p>
        <a routerLink="/" class="btn btn-primary">Back to browse</a>
      </div>
    } @else if (book) {
      <nav class="breadcrumb">
        <a routerLink="/">Browse</a>
        <span> / </span>
        <span>{{ book.title }}</span>
      </nav>

      <article class="book-detail" data-testid="book-detail">
        <div class="book-detail-cover">
          <span class="book-cover-letter large">{{ book.title[0] | uppercase }}</span>
        </div>

        <div class="book-detail-info">
          <h1 data-testid="book-title">{{ book.title }}</h1>
          <p class="book-detail-author">by {{ book.author }}</p>
          <p class="book-detail-price">{{ formatPrice(book.price) }}</p>
          <p class="book-detail-stock" [class.low]="book.stock < 3">{{ book.stock }} copies available</p>

          @if (book.description) {
            <p class="book-detail-desc">{{ book.description }}</p>
          }

          @if (auth.isLoggedIn && book.stock > 0) {
            <div class="detail-form">
              <label class="qty-label">
                Quantity
                <input
                  type="number"
                  class="qty-input"
                  min="1"
                  [max]="book.stock"
                  [(ngModel)]="quantity"
                  data-testid="book-qty"
                />
              </label>
              <button type="button" class="btn btn-primary" data-testid="book-add-to-cart" (click)="addToCart()">
                Add to cart
              </button>
            </div>
          } @else if (!auth.isLoggedIn) {
            <a routerLink="/login" class="btn btn-primary">Login to purchase</a>
          } @else {
            <button class="btn btn-ghost" disabled>Currently out of stock</button>
          }

          @if (error) {
            <div class="alert alert-error">{{ error }}</div>
          }
        </div>
      </article>
    }
  `,
})
export class BookComponent implements OnInit {
  private readonly route = inject(ActivatedRoute);
  private readonly bookService = inject(BookService);
  private readonly cartService = inject(CartService);
  readonly auth = inject(AuthService);

  book: Book | null = null;
  notFound = false;
  quantity = 1;
  error = '';

  ngOnInit(): void {
    this.route.paramMap.subscribe((params) => {
      const id = Number(params.get('id'));
      void this.loadBook(id);
    });
  }

  async loadBook(id: number): Promise<void> {
    this.error = '';
    this.notFound = false;
    try {
      this.book = await this.bookService.get(id);
    } catch {
      this.book = null;
      this.notFound = true;
    }
  }

  async addToCart(): Promise<void> {
    if (!this.book) {
      return;
    }

    const message = await this.cartService.add(this.book.id, this.quantity);
    if (message) {
      this.error = message;
    }
  }

  formatPrice(amount: number): string {
    return `$${amount.toFixed(2)}`;
  }
}
