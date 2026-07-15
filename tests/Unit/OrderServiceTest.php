<?php

declare(strict_types=1);

class OrderServiceTest extends TestCase
{
    private int $userId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userId = $this->createUser();
    }

    public function testCheckoutEmptyCartFails(): void
    {
        $orders = $this->orderService();
        $result = $orders->checkout($this->userId);

        $this->assertFalse($result['ok']);
        $this->assertSame('Your cart is empty.', $result['error']);
    }

    public function testCheckoutCreatesOrderAndClearsCart(): void
    {
        $cart = $this->cartService();
        $orders = $this->orderService();
        $books = $this->bookService();
        $book = $books->find(1);
        $bookId = (int) $book['id'];

        $cart->add($this->userId, $bookId, 1);
        $result = $orders->checkout($this->userId);

        $this->assertTrue($result['ok']);
        $this->assertArrayHasKey('order_id', $result);
        $this->assertCount(0, $cart->items($this->userId));

        $userOrders = $orders->forUser($this->userId);
        $this->assertCount(1, $userOrders);

        $items = $orders->items((int) $userOrders[0]['id']);
        $this->assertCount(1, $items);
        $this->assertSame($book['title'], $items[0]['title']);
    }

    public function testCheckoutReducesStock(): void
    {
        $cart = $this->cartService();
        $orders = $this->orderService();
        $books = $this->bookService();
        $bookId = $this->firstBookId();
        $initialStock = (int) $books->find($bookId)['stock'];

        $cart->add($this->userId, $bookId, 2);
        $orders->checkout($this->userId);

        $updated = $books->find($bookId);
        $this->assertSame($initialStock - 2, (int) $updated['stock']);
    }

    public function testCheckoutFailsWhenStockInsufficient(): void
    {
        $db = Database::getInstance();
        $bookId = $this->firstBookId();
        $db->prepare('UPDATE books SET stock = 0 WHERE id = ?')->execute([$bookId]);

        $cart = $this->cartService();
        $orders = $this->orderService();

        $db->prepare('INSERT INTO cart_items (user_id, book_id, quantity) VALUES (?, ?, ?)')
            ->execute([$this->userId, $bookId, 1]);

        $result = $orders->checkout($this->userId);

        $this->assertFalse($result['ok']);
        $this->assertStringContainsString('out of stock', $result['error']);
    }

    public function testForUserOnlyReturnsOwnOrders(): void
    {
        $otherUserId = $this->createUser('other@example.com');
        $cart = $this->cartService();
        $orders = $this->orderService();

        $cart->add($this->userId, $this->firstBookId(), 1);
        $orders->checkout($this->userId);

        $this->assertCount(1, $orders->forUser($this->userId));
        $this->assertCount(0, $orders->forUser($otherUserId));
    }
}
