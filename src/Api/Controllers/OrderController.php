<?php

declare(strict_types=1);

final class OrderController
{
    public function __construct(private readonly OrderService $orders)
    {
    }

    public function checkout(): never
    {
        $userId = AuthContext::requireUserId();
        $result = $this->orders->checkout($userId);

        if (!$result['ok']) {
            JsonResponse::error($result['error'], JsonResponse::statusForCode($result['code'] ?? null));
        }

        $order = $this->orders->findForUser((int) $result['order_id'], $userId);
        JsonResponse::json($order, 201);
    }

    public function index(): never
    {
        $userId = AuthContext::requireUserId();
        JsonResponse::json($this->orders->ordersForUser($userId));
    }

    public function show(int $orderId): never
    {
        $userId = AuthContext::requireUserId();
        $order = $this->orders->findForUser($orderId, $userId);

        if (!$order) {
            JsonResponse::error('Order not found', 404);
        }

        JsonResponse::json($order);
    }
}
