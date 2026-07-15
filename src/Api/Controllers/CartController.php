<?php

declare(strict_types=1);

final class CartController
{
    public function __construct(private readonly CartService $cart)
    {
    }

    public function show(): never
    {
        $userId = AuthContext::requireUserId();
        JsonResponse::json($this->payload($userId));
    }

    public function addItem(): never
    {
        $userId = AuthContext::requireUserId();
        $body = Request::jsonBody();
        $bookId = (int) ($body['book_id'] ?? 0);
        $quantity = max(1, (int) ($body['quantity'] ?? 1));

        $result = $this->cart->add($userId, $bookId, $quantity);
        if (!$result['ok']) {
            JsonResponse::error($result['error'], JsonResponse::statusForCode($result['code'] ?? null));
        }

        $item = $this->cart->findItem($userId, (int) $result['item_id']);
        JsonResponse::json(ApiFormatter::cartItem($item), 201);
    }

    public function updateItem(int $itemId): never
    {
        $userId = AuthContext::requireUserId();
        $body = Request::jsonBody();
        $quantity = max(1, (int) ($body['quantity'] ?? 1));

        $result = $this->cart->update($userId, $itemId, $quantity);
        if (!$result['ok']) {
            JsonResponse::error($result['error'], JsonResponse::statusForCode($result['code'] ?? null));
        }

        $item = $this->cart->findItem($userId, $itemId);
        JsonResponse::json(ApiFormatter::cartItem($item));
    }

    public function removeItem(int $itemId): never
    {
        $userId = AuthContext::requireUserId();
        $result = $this->cart->remove($userId, $itemId);
        if (!$result['ok']) {
            JsonResponse::error($result['error'], JsonResponse::statusForCode($result['code'] ?? null));
        }

        JsonResponse::noContent();
    }

    public function clear(): never
    {
        $this->cart->clear(AuthContext::requireUserId());
        JsonResponse::noContent();
    }

    private function payload(int $userId): array
    {
        return [
            'items' => array_map(
                static fn (array $item) => ApiFormatter::cartItem($item),
                $this->cart->items($userId)
            ),
            'total' => $this->cart->total($userId),
        ];
    }
}
