import { Injectable } from '@angular/core';
import { firstValueFrom } from 'rxjs';
import { ApiService } from './api.service';
import { Book } from '../models';

@Injectable({ providedIn: 'root' })
export class BookService {
  constructor(private readonly api: ApiService) {}

  list(query = ''): Promise<Book[]> {
    const path = query.trim() ? `/api/books?q=${encodeURIComponent(query.trim())}` : '/api/books';
    return firstValueFrom(this.api.get<Book[]>(path));
  }

  get(id: number): Promise<Book> {
    return firstValueFrom(this.api.get<Book>(`/api/books/${id}`));
  }
}
