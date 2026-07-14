export interface Book {
  id: number;
  title: string;
  author: string;
  price: number;
  stock: number;
  description?: string;
}

export interface User {
  id: number;
  email: string;
}

export interface CartItem {
  id: number;
  book_id: number;
  quantity: number;
  book: Book;
}

export interface Cart {
  items: CartItem[];
  total: number;
}

export interface OrderItem {
  id: number;
  book_id: number;
  quantity: number;
  unit_price: number;
  book: Book;
}

export interface Order {
  id: number;
  total: number;
  status: string;
  created_at: string;
  items: OrderItem[];
}

export interface LoginResponse {
  access_token: string;
  token_type: string;
}

export interface ApiError {
  detail: string;
}
