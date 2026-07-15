# Bookshop PHP

A demo bookshop with a PHP JSON API backend and an Angular frontend, backed by SQLite.

[![CI](https://github.com/ZsoltDunai/bookshop-php/actions/workflows/ci.yml/badge.svg)](https://github.com/ZsoltDunai/bookshop-php/actions/workflows/ci.yml)

## Features

- Browse a catalog of books with search
- User registration and login (JWT bearer tokens)
- Shopping cart with quantity management
- Checkout and order history
- Angular SPA served from the same PHP host
- Self-contained SQLite database (auto-seeded on first run)
- Docker support

## Quick Start

### Docker (recommended)

```bash
docker compose up --build
```

Open http://localhost:8080

### Local development

Requires PHP 8.1+ with PDO SQLite, Node.js 20+, and Composer.

```bash
cd bookshop-php
composer install
cd frontend && npm install && npm run build && cd ..
php -S localhost:8080 -t public router.php
```

Open http://localhost:8080

For frontend hot reload during development:

```bash
# Terminal 1 — API
php -S localhost:8080 -t public router.php

# Terminal 2 — Angular dev server (proxies /api to PHP)
cd frontend && npm start
```

Open http://localhost:4200

## Demo Account

- **Email:** `demo@bookshop.io`
- **Password:** `password123`

## Project Structure

```
bookshop-php/
├── frontend/           # Angular SPA (build output -> public/)
├── public/
│   ├── index.php     # API router + SPA fallback
│   └── index.html    # Built Angular app
├── src/
│   ├── Validation/     # Request validators (auth, cart)
│   ├── Api/Controllers/
│   ├── Http/
│   ├── App.php         # Composition root
│   └── ...
├── tests/
│   ├── Unit/           # Service + validator unit tests
│   ├── Sql/            # Direct SQLite persistence assertions
│   ├── Api/            # HTTP API contract + validation tests
│   ├── Integration/    # Broader HTTP flows
│   └── e2e/            # Playwright UI tests
├── data/               # SQLite database (created at runtime)
└── router.php          # Dev server router
```

## Backend validation

Controllers validate JSON before calling domain services:

| Endpoint | Rules |
|----------|-------|
| `POST /api/auth/register` | email + password required; email format; password ≥ 6 chars; unique email |
| `POST /api/auth/login` | email + password required |
| `POST /api/cart/items` | `book_id` positive int; `quantity` positive int (default 1) |
| `PATCH /api/cart/items/{id}` | `quantity` required positive int |

Invalid input → `400` with `{ "detail": "..." }`. Domain not-found → `404`, duplicate email → `409`.

## API Endpoints

| Method | Route | Description |
|--------|-------|-------------|
| GET | `/health` | Health check |
| GET | `/api/books` | List books (`?q=` to search) |
| GET | `/api/books/{id}` | Book detail |
| POST | `/api/auth/register` | Create account |
| POST | `/api/auth/login` | Login, returns JWT |
| GET | `/api/auth/me` | Current user (auth required) |
| GET | `/api/cart` | Cart contents |
| POST | `/api/cart/items` | Add to cart |
| PATCH | `/api/cart/items/{id}` | Update quantity |
| DELETE | `/api/cart/items/{id}` | Remove item |
| POST | `/api/orders/checkout` | Place order |
| GET | `/api/orders` | Order history |

## Frontend Routes

| Route | Description |
|-------|-------------|
| `/` | Browse books, search |
| `/book/:id` | Book detail |
| `/login` | Sign in |
| `/register` | Create account |
| `/cart` | Shopping cart |
| `/orders` | Order history |

## Reset Database

Delete the SQLite file and restart:

```bash
rm data/bookshop.sqlite
```

The database will be recreated and re-seeded on next request.

## CI

GitHub Actions runs on every push and pull request to `main`/`master`:

| Job | What it runs |
|-----|----------------|
| **Build Angular frontend** | `npm run build` into `public/` |
| **PHPUnit** | Unit + SQL + API + integration tests |

### Run tests locally

```bash
composer install
cd frontend && npm install && npm run build && cd ..
vendor/bin/phpunit
composer test:unit
composer test:sql
composer test:api
bash scripts/ci-smoke.sh   # with server running
cd tests/e2e && npm install && npx playwright install chromium && npm test
```
