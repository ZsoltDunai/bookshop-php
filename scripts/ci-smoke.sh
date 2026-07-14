#!/usr/bin/env bash
set -euo pipefail

BASE_URL="${BASE_URL:-http://127.0.0.1:8080}"
COOKIE_JAR="$(mktemp)"
trap 'rm -f "$COOKIE_JAR"' EXIT

echo "==> Health check"
health="$(curl -fsS "$BASE_URL/health")"
echo "$health" | grep -q '"status":"ok"'
echo "$health" | grep -q '"app":"bookshop-php"'

echo "==> Home page"
home="$(curl -fsS "$BASE_URL/")"
echo "$home" | grep -q "Discover your next great read"
echo "$home" | grep -q "The Great Gatsby"

echo "==> Search"
search="$(curl -fsS "$BASE_URL/?q=Orwell")"
echo "$search" | grep -q "1984"
echo "$search" | grep -qi "orwell"

echo "==> Book detail"
book="$(curl -fsS "$BASE_URL/book?id=1")"
echo "$book" | grep -q "The Great Gatsby"
echo "$book" | grep -qi "fitzgerald"

echo "==> Login page"
login_page="$(curl -fsS "$BASE_URL/login")"
echo "$login_page" | grep -q "Welcome back"

echo "==> Cart requires login"
cart_redirect="$(curl -sS -o /dev/null -w "%{http_code} %{redirect_url}" "$BASE_URL/cart")"
echo "$cart_redirect" | grep -q "302"
echo "$cart_redirect" | grep -q "/login"

echo "==> Demo login"
curl -fsS -c "$COOKIE_JAR" -b "$COOKIE_JAR" \
  -X POST "$BASE_URL/login" \
  -d "email=demo@bookshop.io" \
  -d "password=password123" \
  -o /dev/null

echo "==> Add to cart"
curl -fsS -c "$COOKIE_JAR" -b "$COOKIE_JAR" \
  -X POST "$BASE_URL/cart/add" \
  -d "book_id=1" \
  -d "quantity=1" \
  -d "redirect=/cart" \
  -o /dev/null

echo "==> Cart page"
cart="$(curl -fsS -c "$COOKIE_JAR" -b "$COOKIE_JAR" "$BASE_URL/cart")"
echo "$cart" | grep -q "The Great Gatsby"
echo "$cart" | grep -q "Checkout"

echo "==> Checkout"
curl -fsS -c "$COOKIE_JAR" -b "$COOKIE_JAR" \
  -X POST "$BASE_URL/checkout" \
  -o /dev/null

echo "==> Orders page"
orders="$(curl -fsS -c "$COOKIE_JAR" -b "$COOKIE_JAR" "$BASE_URL/orders")"
echo "$orders" | grep -q "Order #"
echo "$orders" | grep -q "The Great Gatsby"

echo "==> 404 page"
not_found="$(curl -sS -o /tmp/not-found.html -w "%{http_code}" "$BASE_URL/does-not-exist")"
test "$not_found" = "404"
grep -q "404" /tmp/not-found.html

echo "All smoke tests passed."
