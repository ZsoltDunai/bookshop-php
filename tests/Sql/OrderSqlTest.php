<?php

declare(strict_types=1);

/**
 * SQL / persistence tests for checkout and order rows.
 */
class OrderSqlTest extends TestCase
{
    public function testCheckoutInsertsOrderAndOrderItemsThenClearsCart(): void
    {
        $userId = $this->createUser();
        $book = $this->bookService()->find(1);
        $bookId = (int) $book['id'];

        $this->cartService()->add($userId, $bookId, 2);
        $result = $this->orderService()->checkout($userId);

        $this->assertTrue($result['ok']);
        $orderId = (int) $result['order_id'];

        $order = $this->db()->prepare('SELECT user_id, total, status FROM orders WHERE id = ?');
        $order->execute([$orderId]);
        $orderRow = $order->fetch();

        $this->assertNotFalse($orderRow);
        $this->assertSame($userId, (int) $orderRow['user_id']);
        $this->assertSame('completed', $orderRow['status']);
        $this->assertEqualsWithDelta((float) $book['price'] * 2, (float) $orderRow['total'], 0.001);

        $items = $this->db()->prepare(
            'SELECT book_id, title, quantity, price FROM order_items WHERE order_id = ?'
        );
        $items->execute([$orderId]);
        $itemRows = $items->fetchAll();

        $this->assertCount(1, $itemRows);
        $this->assertSame($bookId, (int) $itemRows[0]['book_id']);
        $this->assertSame($book['title'], $itemRows[0]['title']);
        $this->assertSame(2, (int) $itemRows[0]['quantity']);

        $cartCount = $this->db()->prepare('SELECT COUNT(*) FROM cart_items WHERE user_id = ?');
        $cartCount->execute([$userId]);
        $this->assertSame(0, (int) $cartCount->fetchColumn());
    }

    public function testCheckoutDecrementsBookStockInDatabase(): void
    {
        $userId = $this->createUser();
        $bookId = $this->firstBookId();

        $before = $this->db()->prepare('SELECT stock FROM books WHERE id = ?');
        $before->execute([$bookId]);
        $stockBefore = (int) $before->fetchColumn();

        $this->cartService()->add($userId, $bookId, 3);
        $this->orderService()->checkout($userId);

        $after = $this->db()->prepare('SELECT stock FROM books WHERE id = ?');
        $after->execute([$bookId]);
        $this->assertSame($stockBefore - 3, (int) $after->fetchColumn());
    }

    public function testUniqueEmailConstraintOnUsers(): void
    {
        $this->createUser('dup@example.com');

        $this->expectException(PDOException::class);
        $stmt = $this->db()->prepare('INSERT INTO users (email, password_hash) VALUES (?, ?)');
        $stmt->execute(['dup@example.com', 'hash']);
    }
}
