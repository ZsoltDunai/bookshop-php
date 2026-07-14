import { Routes } from '@angular/router';
import { authGuard } from './guards/auth.guard';

export const routes: Routes = [
  { path: '', loadComponent: () => import('./pages/shop/shop.component').then((m) => m.ShopComponent) },
  { path: 'book/:id', loadComponent: () => import('./pages/book/book.component').then((m) => m.BookComponent) },
  { path: 'login', loadComponent: () => import('./pages/login/login.component').then((m) => m.LoginComponent) },
  { path: 'register', loadComponent: () => import('./pages/register/register.component').then((m) => m.RegisterComponent) },
  { path: 'cart', canActivate: [authGuard], loadComponent: () => import('./pages/cart/cart.component').then((m) => m.CartComponent) },
  { path: 'orders', canActivate: [authGuard], loadComponent: () => import('./pages/orders/orders.component').then((m) => m.OrdersComponent) },
  { path: '**', loadComponent: () => import('./pages/not-found/not-found.component').then((m) => m.NotFoundComponent) },
];
