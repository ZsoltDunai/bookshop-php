import { Injectable, signal } from '@angular/core';
import { firstValueFrom } from 'rxjs';
import { ApiService } from './api.service';
import { Cart, CartItem } from '../models';
import { AuthService } from './auth.service';

@Injectable({ providedIn: 'root' })
export class CartService {
  readonly cart = signal<Cart>({ items: [], total: 0 });

  constructor(
    private readonly api: ApiService,
    private readonly auth: AuthService,
  ) {}

  get itemCount(): number {
    return this.cart().items.reduce((sum, item) => sum + item.quantity, 0);
  }

  async refresh(): Promise<void> {
    if (!this.auth.isLoggedIn) {
      this.cart.set({ items: [], total: 0 });
      return;
    }

    const cart = await firstValueFrom(this.api.get<Cart>('/api/cart'));
    this.cart.set(cart);
  }

  async add(bookId: number, quantity = 1): Promise<string | null> {
    try {
      await firstValueFrom(
        this.api.post<CartItem>('/api/cart/items', { book_id: bookId, quantity }),
      );
      await this.refresh();
      return null;
    } catch (error) {
      return ApiService.errorMessage(error);
    }
  }

  async update(itemId: number, quantity: number): Promise<string | null> {
    try {
      await firstValueFrom(this.api.patch<CartItem>(`/api/cart/items/${itemId}`, { quantity }));
      await this.refresh();
      return null;
    } catch (error) {
      return ApiService.errorMessage(error);
    }
  }

  async remove(itemId: number): Promise<string | null> {
    try {
      await firstValueFrom(this.api.delete(`/api/cart/items/${itemId}`));
      await this.refresh();
      return null;
    } catch (error) {
      return ApiService.errorMessage(error);
    }
  }

  async checkout(): Promise<{ ok: true } | { ok: false; error: string }> {
    try {
      await firstValueFrom(this.api.post('/api/orders/checkout'));
      await this.refresh();
      return { ok: true };
    } catch (error) {
      return { ok: false, error: ApiService.errorMessage(error) };
    }
  }
}
