#!/usr/bin/env bash
set -euo pipefail

BASE_URL="${BASE_URL:-http://127.0.0.1:8080}"
TOKEN=""

echo "==> Health check"
health="$(curl -fsS "$BASE_URL/health")"
echo "$health" | grep -q '"status":"ok"'
echo "$health" | grep -q '"app":"bookshop-php"'

echo "==> SPA shell"
home="$(curl -fsS "$BASE_URL/")"
echo "$home" | grep -q "<app-root>"

echo "==> Books API"
books="$(curl -fsS "$BASE_URL/api/books")"
echo "$books" | grep -q "The Great Gatsby"

echo "==> Search API"
search="$(curl -fsS "$BASE_URL/api/books?q=Orwell")"
echo "$search" | grep -q "1984"
echo "$search" | grep -qi "orwell"

echo "==> Book detail API"
book="$(curl -fsS "$BASE_URL/api/books/1")"
echo "$book" | grep -q "The Great Gatsby"
echo "$book" | grep -qi "fitzgerald"

echo "==> Cart requires auth"
cart_status="$(curl -sS -o /dev/null -w "%{http_code}" "$BASE_URL/api/cart")"
test "$cart_status" = "401"

echo "==> Invalid login"
invalid_login="$(curl -sS -o /tmp/invalid-login.json -w "%{http_code}" \
  -H "Content-Type: application/json" \
  -d '{"email":"demo@bookshop.io","password":"wrong"}' \
  "$BASE_URL/api/auth/login")"
test "$invalid_login" = "401"
grep -q "Invalid credentials" /tmp/invalid-login.json

echo "==> Register new user"
REGISTER_EMAIL="ci-user-$(date +%s)@bookshop.io"
curl -fsS -H "Content-Type: application/json" \
  -d "{\"email\":\"$REGISTER_EMAIL\",\"password\":\"password123\"}" \
  "$BASE_URL/api/auth/register" > /tmp/register.json

echo "==> Demo login"
login_response="$(curl -fsS -H "Content-Type: application/json" \
  -d '{"email":"demo@bookshop.io","password":"password123"}' \
  "$BASE_URL/api/auth/login")"
TOKEN="$(echo "$login_response" | sed -n 's/.*"access_token":"\([^"]*\)".*/\1/p')"
test -n "$TOKEN"

echo "==> Add to cart"
curl -fsS -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{"book_id":1,"quantity":1}' \
  "$BASE_URL/api/cart/items" > /dev/null

echo "==> Cart API"
cart="$(curl -fsS -H "Authorization: Bearer $TOKEN" "$BASE_URL/api/cart")"
echo "$cart" | grep -q "The Great Gatsby"

echo "==> Checkout"
curl -fsS -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -X POST "$BASE_URL/api/orders/checkout" > /dev/null

echo "==> Orders API"
orders="$(curl -fsS -H "Authorization: Bearer $TOKEN" "$BASE_URL/api/orders")"
echo "$orders" | grep -q "The Great Gatsby"

echo "All smoke tests passed."
