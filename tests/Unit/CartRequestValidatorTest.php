<?php

declare(strict_types=1);

class CartRequestValidatorTest extends TestCase
{
    public function testAddItemRequiresBookId(): void
    {
        $result = CartRequestValidator::addItem(['quantity' => 1]);

        $this->assertFalse($result['ok']);
        $this->assertSame('book_id must be a positive integer.', $result['error']);
    }

    public function testAddItemRejectsZeroQuantity(): void
    {
        $result = CartRequestValidator::addItem(['book_id' => 1, 'quantity' => 0]);

        $this->assertFalse($result['ok']);
        $this->assertSame('quantity must be a positive integer.', $result['error']);
    }

    public function testAddItemDefaultsQuantityToOne(): void
    {
        $result = CartRequestValidator::addItem(['book_id' => 2]);

        $this->assertTrue($result['ok']);
        $this->assertSame(2, $result['data']['book_id']);
        $this->assertSame(1, $result['data']['quantity']);
    }

    public function testUpdateItemRequiresQuantity(): void
    {
        $result = CartRequestValidator::updateItem([]);

        $this->assertFalse($result['ok']);
        $this->assertSame('quantity is required.', $result['error']);
    }
}
