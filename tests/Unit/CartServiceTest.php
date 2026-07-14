<?php

declare(strict_types=1);

class CartServiceTest extends TestCase
{
    private int $userId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userId = $this->createUser();
    }

    public function testAddBookToEmptyCart(): void
    {
        $cart = new CartService();
        $bookId = $this->firstBookId();

        $result = $cart->add($this->userId, $bookId, 1);

        $this->assertTrue($result['ok']);
        $this->assertCount(1, $cart->items($this->userId));
        $this->assertSame(1, $cart->itemCount($this->userId));
    }

    public function testAddSameBookMergesQuantity(): void
    {
        $cart = new CartService();
        $bookId = $this->firstBookId();

        $cart->add($this->userId, $bookId, 1);
        $cart->add($this->userId, $bookId, 2);

        $items = $cart->items($this->userId);

        $this->assertCount(1, $items);
        $this->assertSame(3, $items[0]['quantity']);
    }

    public function testAddMoreThanStockFails(): void
    {
        $cart = new CartService();
        $bookId = $this->firstBookId();

        $result = $cart->add($this->userId, $bookId, 999);

        $this->assertFalse($result['ok']);
        $this->assertSame('Not enough stock available.', $result['error']);
    }

    public function testAddMissingBookFails(): void
    {
        $cart = new CartService();
        $result = $cart->add($this->userId, 9999, 1);

        $this->assertFalse($result['ok']);
        $this->assertSame('Book not found.', $result['error']);
    }

    public function testUpdateQuantity(): void
    {
        $cart = new CartService();
        $bookId = $this->firstBookId();
        $cart->add($this->userId, $bookId, 1);

        $itemId = (int) $cart->items($this->userId)[0]['id'];
        $result = $cart->update($this->userId, $itemId, 2);

        $this->assertTrue($result['ok']);
        $this->assertSame(2, $cart->items($this->userId)[0]['quantity']);
    }

    public function testUpdateToZeroRemovesItem(): void
    {
        $cart = new CartService();
        $bookId = $this->firstBookId();
        $cart->add($this->userId, $bookId, 1);

        $itemId = (int) $cart->items($this->userId)[0]['id'];
        $result = $cart->update($this->userId, $itemId, 0);

        $this->assertTrue($result['ok']);
        $this->assertCount(0, $cart->items($this->userId));
    }

    public function testRemoveItem(): void
    {
        $cart = new CartService();
        $bookId = $this->firstBookId();
        $cart->add($this->userId, $bookId, 1);

        $itemId = (int) $cart->items($this->userId)[0]['id'];
        $result = $cart->remove($this->userId, $itemId);

        $this->assertTrue($result['ok']);
        $this->assertCount(0, $cart->items($this->userId));
    }

    public function testTotalCalculation(): void
    {
        $cart = new CartService();
        $books = new BookService();
        $first = $books->find(1);
        $second = $books->find(2);

        $cart->add($this->userId, (int) $first['id'], 1);
        $cart->add($this->userId, (int) $second['id'], 2);

        $expected = (float) $first['price'] + ((float) $second['price'] * 2);

        $this->assertEqualsWithDelta($expected, $cart->total($this->userId), 0.001);
    }
}
