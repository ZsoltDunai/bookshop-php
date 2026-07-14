import { Injectable } from '@angular/core';
import { firstValueFrom } from 'rxjs';
import { ApiService } from './api.service';
import { Order } from '../models';

@Injectable({ providedIn: 'root' })
export class OrderService {
  constructor(private readonly api: ApiService) {}

  list(): Promise<Order[]> {
    return firstValueFrom(this.api.get<Order[]>('/api/orders'));
  }
}
