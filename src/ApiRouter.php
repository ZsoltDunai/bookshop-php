<?php

declare(strict_types=1);

final class ApiRouter
{
    public static function dispatch(string $method, string $path): void
    {
        $auth = new Auth();
        $books = new BookService();
        $cart = new CartService();
        $orders = new OrderService();

        if ($method === 'GET' && $path === '/api/books') {
            $query = trim((string) ($_GET['q'] ?? ''));
            $rows = $query !== '' ? $books->search($query) : $books->all();
            $payload = array_map(
                fn (array $book) => ApiFormatter::book($book),
                $rows
            );
            JsonResponse::json($payload);
        }

        if ($method === 'GET' && preg_match('#^/api/books/(\d+)$#', $path, $matches)) {
            $book = $books->find((int) $matches[1]);
            if (!$book) {
                JsonResponse::error('Book not found', 404);
            }
            JsonResponse::json(ApiFormatter::book($book));
        }

        if ($method === 'POST' && $path === '/api/auth/register') {
            $body = JsonResponse::readJsonBody();
            $result = $auth->register($body['email'] ?? '', $body['password'] ?? '');
            if (!$result['ok']) {
                JsonResponse::error($result['error'], 400);
            }
            JsonResponse::json(ApiFormatter::user($result['user']), 201);
        }

        if ($method === 'POST' && $path === '/api/auth/login') {
            $body = JsonResponse::readJsonBody();
            $result = $auth->authenticate($body['email'] ?? '', $body['password'] ?? '');
            if (!$result['ok']) {
                JsonResponse::error('Invalid credentials', 401);
            }
            JsonResponse::json([
                'access_token' => JwtAuth::createToken((int) $result['user_id']),
                'token_type' => 'bearer',
            ]);
        }

        if ($method === 'GET' && $path === '/api/auth/me') {
            $user = $auth->findUserById(JsonResponse::requireUserId());
            if (!$user) {
                JsonResponse::error('Could not validate credentials', 401);
            }
            JsonResponse::json(ApiFormatter::user($user));
        }

        if ($method === 'GET' && $path === '/api/cart') {
            $userId = JsonResponse::requireUserId();
            JsonResponse::json(self::cartPayload($cart, $userId));
        }

        if ($method === 'POST' && $path === '/api/cart/items') {
            $userId = JsonResponse::requireUserId();
            $body = JsonResponse::readJsonBody();
            $bookId = (int) ($body['book_id'] ?? 0);
            $quantity = max(1, (int) ($body['quantity'] ?? 1));
            $result = $cart->add($userId, $bookId, $quantity);
            if (!$result['ok']) {
                $status = str_contains($result['error'], 'not found') ? 404 : 400;
                JsonResponse::error($result['error'], $status);
            }
            $item = $cart->findItem($userId, (int) $result['item_id']);
            JsonResponse::json(ApiFormatter::cartItem($item), 201);
        }

        if ($method === 'PATCH' && preg_match('#^/api/cart/items/(\d+)$#', $path, $matches)) {
            $userId = JsonResponse::requireUserId();
            $body = JsonResponse::readJsonBody();
            $quantity = max(1, (int) ($body['quantity'] ?? 1));
            $result = $cart->update($userId, (int) $matches[1], $quantity);
            if (!$result['ok']) {
                $status = str_contains($result['error'], 'not found') ? 404 : 400;
                JsonResponse::error($result['error'], $status);
            }
            $item = $cart->findItem($userId, (int) $matches[1]);
            JsonResponse::json(ApiFormatter::cartItem($item));
        }

        if ($method === 'DELETE' && preg_match('#^/api/cart/items/(\d+)$#', $path, $matches)) {
            $userId = JsonResponse::requireUserId();
            $result = $cart->remove($userId, (int) $matches[1]);
            if (!$result['ok']) {
                JsonResponse::error($result['error'], 404);
            }
            JsonResponse::noContent();
        }

        if ($method === 'DELETE' && $path === '/api/cart') {
            $cart->clear(JsonResponse::requireUserId());
            JsonResponse::noContent();
        }

        if ($method === 'POST' && $path === '/api/orders/checkout') {
            $userId = JsonResponse::requireUserId();
            $result = $orders->checkout($userId);
            if (!$result['ok']) {
                JsonResponse::error($result['error'], 400);
            }
            $order = $orders->findForUser((int) $result['order_id'], $userId);
            JsonResponse::json($order, 201);
        }

        if ($method === 'GET' && $path === '/api/orders') {
            $userId = JsonResponse::requireUserId();
            $payload = array_map(
                fn (array $order) => $order,
                $orders->ordersForUser($userId)
            );
            JsonResponse::json($payload);
        }

        if ($method === 'GET' && preg_match('#^/api/orders/(\d+)$#', $path, $matches)) {
            $userId = JsonResponse::requireUserId();
            $order = $orders->findForUser((int) $matches[1], $userId);
            if (!$order) {
                JsonResponse::error('Order not found', 404);
            }
            JsonResponse::json($order);
        }

        JsonResponse::error('Not found', 404);
    }

    private static function cartPayload(CartService $cart, int $userId): array
    {
        $items = array_map(
            fn (array $item) => ApiFormatter::cartItem($item),
            $cart->items($userId)
        );

        return [
            'items' => $items,
            'total' => $cart->total($userId),
        ];
    }
}
