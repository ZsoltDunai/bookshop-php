<?php

declare(strict_types=1);

class OrderService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function checkout(int $userId): array
    {
        $cart = new CartService();
        $items = $cart->items($userId);

        if (empty($items)) {
            return ['ok' => false, 'error' => 'Your cart is empty.'];
        }

        try {
            $this->db->beginTransaction();

            foreach ($items as $item) {
                $stmt = $this->db->prepare('SELECT stock FROM books WHERE id = ?');
                $stmt->execute([$item['book_id']]);
                $book = $stmt->fetch();

                if (!$book || (int) $book['stock'] < (int) $item['quantity']) {
                    $this->db->rollBack();
                    return ['ok' => false, 'error' => '"' . $item['title'] . '" is out of stock.'];
                }
            }

            $total = $cart->total($userId);

            $stmt = $this->db->prepare('INSERT INTO orders (user_id, total) VALUES (?, ?)');
            $stmt->execute([$userId, $total]);
            $orderId = (int) $this->db->lastInsertId();

            $orderItemStmt = $this->db->prepare('
                INSERT INTO order_items (order_id, book_id, title, author, price, quantity)
                VALUES (?, ?, ?, ?, ?, ?)
            ');
            $stockStmt = $this->db->prepare('UPDATE books SET stock = stock - ? WHERE id = ?');

            foreach ($items as $item) {
                $orderItemStmt->execute([
                    $orderId,
                    $item['book_id'],
                    $item['title'],
                    $item['author'],
                    $item['price'],
                    $item['quantity'],
                ]);
                $stockStmt->execute([$item['quantity'], $item['book_id']]);
            }

            $cart->clear($userId);
            $this->db->commit();

            return ['ok' => true, 'order_id' => $orderId];
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            return ['ok' => false, 'error' => 'Checkout failed. Please try again.'];
        }
    }

    public function ordersForUser(int $userId): array
    {
        $orders = $this->forUser($userId);
        $payload = [];

        foreach ($orders as $order) {
            $formatted = $this->findForUser((int) $order['id'], $userId);
            if ($formatted) {
                $payload[] = $formatted;
            }
        }

        return $payload;
    }

    public function findForUser(int $orderId, int $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ?');
        $stmt->execute([$orderId, $userId]);
        $order = $stmt->fetch();

        if (!$order) {
            return null;
        }

        $items = $this->items($orderId);
        $mappedItems = array_map(static function (array $item): array {
            $item['unit_price'] = (float) $item['price'];

            return $item;
        }, $items);

        return ApiFormatter::order($order, $mappedItems);
    }

    public function forUser(int $userId): array
    {
        $stmt = $this->db->prepare('
            SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC
        ');
        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }

    public function items(int $orderId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM order_items WHERE order_id = ?');
        $stmt->execute([$orderId]);

        return $stmt->fetchAll();
    }
}
