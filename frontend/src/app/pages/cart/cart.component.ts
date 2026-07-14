import { UpperCasePipe } from '@angular/common';
import { Component, OnInit, inject } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { CartItem } from '../../models';
import { CartService } from '../../services/cart.service';

@Component({
  selector: 'app-cart',
  standalone: true,
  imports: [RouterLink, FormsModule, UpperCasePipe],
  template: `
    <section class="page-header">
      <h1 data-testid="cart-heading">Your cart</h1>
    </section>

    @if (error) {
      <div class="alert alert-error">{{ error }}</div>
    }

    @if (items.length === 0) {
      <div class="empty-state" data-testid="cart-empty">
        <p>Your cart is empty.</p>
        <a routerLink="/" class="btn btn-primary">Browse books</a>
      </div>
    } @else {
      <div class="cart-layout" data-testid="cart-layout">
        <div class="cart-items">
          @for (item of items; track item.id) {
            <div class="cart-item" data-testid="cart-item" [attr.data-item-id]="item.id">
              <div class="cart-item-cover">
                <span>{{ item.book.title[0] | uppercase }}</span>
              </div>
              <div class="cart-item-info">
                <h3>{{ item.book.title }}</h3>
                <p class="cart-item-author">{{ item.book.author }}</p>
                <p class="cart-item-price">{{ formatPrice(item.book.price) }} each</p>
              </div>
              <div class="cart-item-actions">
                <div class="qty-form">
                  <input
                    type="number"
                    class="qty-input"
                    min="1"
                    [max]="item.book.stock"
                    [(ngModel)]="item.quantity"
                    data-testid="cart-qty"
                  />
                  <button type="button" class="btn btn-ghost btn-sm" data-testid="cart-update" (click)="update(item)">
                    Update
                  </button>
                </div>
                <button type="button" class="btn btn-ghost btn-sm text-danger" data-testid="cart-remove" (click)="remove(item.id)">
                  Remove
                </button>
              </div>
              <div class="cart-item-total">
                {{ formatPrice(item.book.price * item.quantity) }}
              </div>
            </div>
          }
        </div>

        <aside class="cart-summary" data-testid="cart-summary">
          <h2>Order summary</h2>
          <div class="summary-row">
            <span>Subtotal</span>
            <span>{{ formatPrice(total) }}</span>
          </div>
          <div class="summary-row">
            <span>Shipping</span>
            <span>Free</span>
          </div>
          <div class="summary-row total" data-testid="cart-total">
            <span>Total</span>
            <span>{{ formatPrice(total) }}</span>
          </div>
          <button type="button" class="btn btn-primary btn-block" data-testid="checkout-btn" (click)="checkout()">
            Checkout
          </button>
          <a routerLink="/" class="btn btn-ghost btn-block">Continue shopping</a>
        </aside>
      </div>
    }
  `,
})
export class CartComponent implements OnInit {
  private readonly cartService = inject(CartService);
  private readonly router = inject(Router);

  items: CartItem[] = [];
  total = 0;
  error = '';

  ngOnInit(): void {
    void this.refresh();
  }

  async refresh(): Promise<void> {
    await this.cartService.refresh();
    const cart = this.cartService.cart();
    this.items = cart.items;
    this.total = cart.total;
  }

  async update(item: CartItem): Promise<void> {
    const message = await this.cartService.update(item.id, item.quantity);
    if (message) {
      this.error = message;
      return;
    }

    await this.refresh();
  }

  async remove(itemId: number): Promise<void> {
    const message = await this.cartService.remove(itemId);
    if (message) {
      this.error = message;
      return;
    }

    await this.refresh();
  }

  async checkout(): Promise<void> {
    this.error = '';
    const result = await this.cartService.checkout();
    if (!result.ok) {
      this.error = result.error;
      return;
    }

    await this.router.navigate(['/orders']);
  }

  formatPrice(amount: number): string {
    return `$${amount.toFixed(2)}`;
  }
}
