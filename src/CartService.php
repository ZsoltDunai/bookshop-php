<?php

declare(strict_types=1);

class CartService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function items(int $userId): array
    {
        $stmt = $this->db->prepare('
            SELECT ci.id, ci.book_id, ci.quantity, b.title, b.author, b.price, b.stock
            FROM cart_items ci
            JOIN books b ON b.id = ci.book_id
            WHERE ci.user_id = ?
            ORDER BY ci.id
        ');
        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }

    public function itemCount(int $userId): int
    {
        $stmt = $this->db->prepare('SELECT COALESCE(SUM(quantity), 0) FROM cart_items WHERE user_id = ?');
        $stmt->execute([$userId]);

        return (int) $stmt->fetchColumn();
    }

    public function total(int $userId): float
    {
        $total = 0.0;
        foreach ($this->items($userId) as $item) {
            $total += (float) $item['price'] * (int) $item['quantity'];
        }

        return $total;
    }

    public function add(int $userId, int $bookId, int $quantity = 1): array
    {
        $bookService = new BookService();
        $book = $bookService->find($bookId);

        if (!$book) {
            return ['ok' => false, 'error' => 'Book not found.'];
        }

        if ($quantity < 1) {
            return ['ok' => false, 'error' => 'Quantity must be at least 1.'];
        }

        $stmt = $this->db->prepare('SELECT id, quantity FROM cart_items WHERE user_id = ? AND book_id = ?');
        $stmt->execute([$userId, $bookId]);
        $existing = $stmt->fetch();

        $newQty = $existing ? (int) $existing['quantity'] + $quantity : $quantity;

        if ($newQty > (int) $book['stock']) {
            return ['ok' => false, 'error' => 'Not enough stock available.'];
        }

        if ($existing) {
            $stmt = $this->db->prepare('UPDATE cart_items SET quantity = ? WHERE id = ?');
            $stmt->execute([$newQty, $existing['id']]);
            $itemId = (int) $existing['id'];
        } else {
            $stmt = $this->db->prepare('INSERT INTO cart_items (user_id, book_id, quantity) VALUES (?, ?, ?)');
            $stmt->execute([$userId, $bookId, $quantity]);
            $itemId = (int) $this->db->lastInsertId();
        }

        return ['ok' => true, 'item_id' => $itemId];
    }

    public function findItem(int $userId, int $itemId): ?array
    {
        $stmt = $this->db->prepare('
            SELECT ci.id, ci.book_id, ci.quantity, b.title, b.author, b.price, b.stock
            FROM cart_items ci
            JOIN books b ON b.id = ci.book_id
            WHERE ci.id = ? AND ci.user_id = ?
        ');
        $stmt->execute([$itemId, $userId]);
        $item = $stmt->fetch();

        return $item ?: null;
    }

    public function update(int $userId, int $cartItemId, int $quantity): array
    {
        $stmt = $this->db->prepare('
            SELECT ci.id, ci.quantity, b.stock
            FROM cart_items ci
            JOIN books b ON b.id = ci.book_id
            WHERE ci.id = ? AND ci.user_id = ?
        ');
        $stmt->execute([$cartItemId, $userId]);
        $item = $stmt->fetch();

        if (!$item) {
            return ['ok' => false, 'error' => 'Cart item not found.'];
        }

        if ($quantity < 1) {
            return $this->remove($userId, $cartItemId);
        }

        if ($quantity > (int) $item['stock']) {
            return ['ok' => false, 'error' => 'Not enough stock available.'];
        }

        $stmt = $this->db->prepare('UPDATE cart_items SET quantity = ? WHERE id = ? AND user_id = ?');
        $stmt->execute([$quantity, $cartItemId, $userId]);

        return ['ok' => true];
    }

    public function remove(int $userId, int $cartItemId): array
    {
        $stmt = $this->db->prepare('DELETE FROM cart_items WHERE id = ? AND user_id = ?');
        $stmt->execute([$cartItemId, $userId]);

        return ['ok' => true];
    }

    public function clear(int $userId): void
    {
        $stmt = $this->db->prepare('DELETE FROM cart_items WHERE user_id = ?');
        $stmt->execute([$userId]);
    }
}
