import { Injectable } from '@angular/core';
import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class ApiService {
  constructor(private readonly http: HttpClient) {}

  get<T>(path: string): Observable<T> {
    return this.http.get<T>(path);
  }

  post<T>(path: string, body: unknown = {}): Observable<T> {
    return this.http.post<T>(path, body);
  }

  patch<T>(path: string, body: unknown): Observable<T> {
    return this.http.patch<T>(path, body);
  }

  delete(path: string): Observable<void> {
    return this.http.delete<void>(path);
  }

  static errorMessage(error: unknown): string {
    if (error instanceof HttpErrorResponse) {
      const detail = error.error?.detail;
      if (typeof detail === 'string') {
        return detail;
      }
    }

    return 'Request failed';
  }

  static rethrow(error: unknown) {
    return throwError(() => error);
  }
}
