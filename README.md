# Bookshop PHP

A simple demo bookshop web application built with plain PHP and SQLite.

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
