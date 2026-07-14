# Bookshop PHP

A simple demo bookshop web application built with plain PHP and SQLite.

[![CI](https://github.com/ZsoltDunai/bookshop-php/actions/workflows/ci.yml/badge.svg)](https://github.com/ZsoltDunai/bookshop-php/actions/workflows/ci.yml)

## Features

- Browse a catalog of books with search
- User registration and login (session-based)
- Shopping cart with quantity management
- Checkout and order history
- Self-contained SQLite database (auto-seeded on first run)
- Docker support

## Quick Start

### Docker (recommended)

```bash
docker compose up --build
```

Open http://localhost:8080

### PHP built-in server

Requires PHP 8.1+ with PDO SQLite extension.

```bash
cd bookshop-php
php -S localhost:8080 -t public router.php
```

Open http://localhost:8080

## Demo Account

- **Email:** `demo@bookshop.io`
- **Password:** `password123`

## Project Structure

```
bookshop-php/
├── public/
│   ├── index.php       # Front controller / router
│   └── css/style.css   # Styles
├── src/
│   ├── bootstrap.php   # App bootstrap, helpers
│   ├── Database.php    # SQLite setup & seeding
│   ├── Auth.php        # Login, register, sessions
│   ├── BookService.php
│   ├── CartService.php
│   └── OrderService.php
├── views/              # PHP templates
├── data/               # SQLite database (created at runtime)
├── router.php          # Dev server router
├── Dockerfile
└── docker-compose.yml
```

## Pages

| Route | Description |
|-------|-------------|
| `/` | Browse books, search |
| `/book?id=1` | Book detail |
| `/login` | Sign in |
| `/register` | Create account |
| `/cart` | Shopping cart |
| `/orders` | Order history |
| `/health` | Health check (JSON) |

## Reset Database

Delete the SQLite file and restart:

```bash
# Local
rm data/bookshop.sqlite

# Docker
docker compose down
rm data/bookshop.sqlite
docker compose up --build
```

The database will be recreated and re-seeded on next request.

## CI

GitHub Actions runs on every push and pull request to `main`/`master`:

| Job | What it runs |
|-----|----------------|
| **PHPUnit** | Unit + integration tests (auth, cart, checkout, security, performance) |
| **Smoke** | Bash curl script for happy-path HTTP checks |
| **Playwright E2E** | Browser UI tests + HTTP contract/security specs |

### Run tests locally

**PHPUnit** (requires PHP 8.1+, curl, Composer):

```bash
composer install
vendor/bin/phpunit
vendor/bin/phpunit --testsuite unit
vendor/bin/phpunit --testsuite integration
```

**Smoke tests** (server must be running on port 8080):

```bash
php -S 127.0.0.1:8080 -t public router.php
bash scripts/ci-smoke.sh
```

**Playwright E2E**:

```bash
cd e2e
npm install
npx playwright install chromium
npm test
```

## Test Structure

```
tests/
├── Unit/              # Fast service-layer tests (in-memory SQLite)
├── Integration/       # HTTP tests against built-in server
└── Support/           # Test server, HTTP client

e2e/
├── ui/                # Browser tests (login, shop, cart, checkout)
└── http/              # Contract + security HTTP specs
```
