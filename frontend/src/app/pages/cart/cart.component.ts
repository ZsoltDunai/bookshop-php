import { UpperCasePipe } from '@angular/common';
import { Component, OnInit, inject } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { CartService } from '../../services/cart.service';
import { PricePipe } from '../../pipes/price.pipe';

@Component({
  selector: 'app-cart',
  standalone: true,
  imports: [RouterLink, FormsModule, UpperCasePipe, PricePipe],
  template: `
    <section class="page-header">
      <h1 data-testid="cart-heading">Your cart</h1>
    </section>

    @if (error) {
      <div class="alert alert-error">{{ error }}</div>
    }

    @if (cartService.cart().items.length === 0) {
      <div class="empty-state" data-testid="cart-empty">
        <p>Your cart is empty.</p>
        <a routerLink="/" class="btn btn-primary">Browse books</a>
      </div>
    } @else {
      <div class="cart-layout" data-testid="cart-layout">
        <div class="cart-items">
          @for (item of cartService.cart().items; track item.id) {
            <div class="cart-item" data-testid="cart-item" [attr.data-item-id]="item.id">
              <div class="cart-item-cover">
                <span>{{ item.book.title[0] | uppercase }}</span>
              </div>
              <div class="cart-item-info">
                <h3>{{ item.book.title }}</h3>
                <p class="cart-item-author">{{ item.book.author }}</p>
                <p class="cart-item-price">{{ item.book.price | price }} each</p>
              </div>
              <div class="cart-item-actions">
                <div class="qty-form">
                  <input
                    type="number"
                    class="qty-input"
                    min="1"
                    [max]="item.book.stock"
                    [ngModel]="item.quantity"
                    (ngModelChange)="quantities[item.id] = $event"
                    data-testid="cart-qty"
                  />
                  <button
                    type="button"
                    class="btn btn-ghost btn-sm"
                    data-testid="cart-update"
                    (click)="update(item.id, quantities[item.id] ?? item.quantity)"
                  >
                    Update
                  </button>
                </div>
                <button type="button" class="btn btn-ghost btn-sm text-danger" data-testid="cart-remove" (click)="remove(item.id)">
                  Remove
                </button>
              </div>
              <div class="cart-item-total">
                {{ item.book.price * item.quantity | price }}
              </div>
            </div>
          }
        </div>

        <aside class="cart-summary" data-testid="cart-summary">
          <h2>Order summary</h2>
          <div class="summary-row">
            <span>Subtotal</span>
            <span>{{ cartService.cart().total | price }}</span>
          </div>
          <div class="summary-row">
            <span>Shipping</span>
            <span>Free</span>
          </div>
          <div class="summary-row total" data-testid="cart-total">
            <span>Total</span>
            <span>{{ cartService.cart().total | price }}</span>
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
  readonly cartService = inject(CartService);
  private readonly router = inject(Router);

  quantities: Record<number, number> = {};
  error = '';

  ngOnInit(): void {
    void this.cartService.refresh();
  }

  async update(itemId: number, quantity: number): Promise<void> {
    const message = await this.cartService.update(itemId, quantity);
    if (message) {
      this.error = message;
    }
  }

  async remove(itemId: number): Promise<void> {
    const message = await this.cartService.remove(itemId);
    if (message) {
      this.error = message;
    }
  }

  async checkout(): Promise<void> {
    this.error = '';
    const message = await this.cartService.checkout();
    if (message) {
      this.error = message;
      return;
    }

    await this.router.navigate(['/orders']);
  }
}
