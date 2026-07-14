import { Component, OnInit, inject } from '@angular/core';
import { RouterLink } from '@angular/router';
import { DatePipe } from '@angular/common';
import { Order } from '../../models';
import { OrderService } from '../../services/order.service';

@Component({
  selector: 'app-orders',
  standalone: true,
  imports: [RouterLink, DatePipe],
  template: `
    <section class="page-header">
      <h1 data-testid="orders-heading">Order history</h1>
    </section>

    @if (error) {
      <div class="alert alert-error">{{ error }}</div>
    }

    @if (orders.length === 0) {
      <div class="empty-state" data-testid="orders-empty">
        <p>You haven't placed any orders yet.</p>
        <a routerLink="/" class="btn btn-primary">Start shopping</a>
      </div>
    } @else {
      <div class="orders-list" data-testid="orders-list">
        @for (order of orders; track order.id) {
          <article class="order-card" data-testid="order-card" [attr.data-order-id]="order.id">
            <header class="order-header">
              <div>
                <h2>Order #{{ order.id }}</h2>
                <time [attr.datetime]="order.created_at">
                  {{ order.created_at | date: 'medium' }}
                </time>
              </div>
              <div class="order-total">{{ formatPrice(order.total) }}</div>
            </header>

            <ul class="order-items">
              @for (item of order.items; track item.id) {
                <li>
                  <span class="order-item-title">{{ item.book.title }}</span>
                  <span class="order-item-qty">×{{ item.quantity }}</span>
                  <span class="order-item-price">{{ formatPrice(item.unit_price * item.quantity) }}</span>
                </li>
              }
            </ul>
          </article>
        }
      </div>
    }
  `,
})
export class OrdersComponent implements OnInit {
  private readonly orderService = inject(OrderService);

  orders: Order[] = [];
  error = '';

  ngOnInit(): void {
    void this.load();
  }

  async load(): Promise<void> {
    try {
      this.orders = await this.orderService.list();
    } catch {
      this.error = 'Failed to load orders.';
      this.orders = [];
    }
  }

  formatPrice(amount: number): string {
    return `$${amount.toFixed(2)}`;
  }
}
