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

  async refresh(): Promise<string | null> {
    if (!this.auth.isLoggedIn) {
      this.cart.set({ items: [], total: 0 });
      return null;
    }

    try {
      const cart = await firstValueFrom(this.api.get<Cart>('/api/cart'));
      this.cart.set(cart);
      return null;
    } catch (error) {
      this.cart.set({ items: [], total: 0 });
      return ApiService.errorMessage(error);
    }
  }

  async add(bookId: number, quantity = 1): Promise<string | null> {
    try {
      await firstValueFrom(
        this.api.post<CartItem>('/api/cart/items', { book_id: bookId, quantity }),
      );
      return this.refresh();
    } catch (error) {
      return ApiService.errorMessage(error);
    }
  }

  async update(itemId: number, quantity: number): Promise<string | null> {
    try {
      await firstValueFrom(this.api.patch<CartItem>(`/api/cart/items/${itemId}`, { quantity }));
      return this.refresh();
    } catch (error) {
      return ApiService.errorMessage(error);
    }
  }

  async remove(itemId: number): Promise<string | null> {
    try {
      await firstValueFrom(this.api.delete(`/api/cart/items/${itemId}`));
      return this.refresh();
    } catch (error) {
      return ApiService.errorMessage(error);
    }
  }

  async checkout(): Promise<string | null> {
    try {
      await firstValueFrom(this.api.post('/api/orders/checkout'));
      return this.refresh();
    } catch (error) {
      return ApiService.errorMessage(error);
    }
  }
}
