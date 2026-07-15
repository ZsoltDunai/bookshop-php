import { Injectable, signal } from '@angular/core';
import { Router } from '@angular/router';
import { firstValueFrom } from 'rxjs';
import { ApiService } from './api.service';
import { LoginResponse, User } from '../models';

const TOKEN_KEY = 'bookshop_token';

@Injectable({ providedIn: 'root' })
export class AuthService {
  readonly user = signal<User | null>(null);

  constructor(
    private readonly api: ApiService,
    private readonly router: Router,
  ) {}

  get token(): string | null {
    return localStorage.getItem(TOKEN_KEY);
  }

  get isLoggedIn(): boolean {
    return !!this.token;
  }

  async loadUser(): Promise<void> {
    if (!this.token) {
      this.user.set(null);
      return;
    }

    try {
      const user = await firstValueFrom(this.api.get<User>('/api/auth/me'));
      this.user.set(user);
    } catch {
      this.clearSession();
    }
  }

  async login(email: string, password: string): Promise<string | null> {
    try {
      const response = await firstValueFrom(
        this.api.post<LoginResponse>('/api/auth/login', { email, password }),
      );
      localStorage.setItem(TOKEN_KEY, response.access_token);
      await this.loadUser();
      return null;
    } catch (error) {
      return ApiService.errorMessage(error);
    }
  }

  async register(email: string, password: string): Promise<string | null> {
    try {
      await firstValueFrom(this.api.post<User>('/api/auth/register', { email, password }));
      return this.login(email, password);
    } catch (error) {
      return ApiService.errorMessage(error);
    }
  }

  logout(): void {
    this.clearSession();
    void this.router.navigate(['/']);
  }

  clearSession(): void {
    localStorage.removeItem(TOKEN_KEY);
    this.user.set(null);
  }
}
