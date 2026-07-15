import { Pipe, PipeTransform } from '@angular/core';

@Pipe({ name: 'price', standalone: true })
export class PricePipe implements PipeTransform {
  transform(amount: number | null | undefined): string {
    const value = Number(amount);
    if (!Number.isFinite(value)) {
      return '$0.00';
    }

    return `$${value.toFixed(2)}`;
  }
}
