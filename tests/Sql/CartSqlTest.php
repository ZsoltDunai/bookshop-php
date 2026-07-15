<?php

declare(strict_types=1);

/**
 * SQL / persistence tests — assert database rows and constraints directly.
 */
class CartSqlTest extends TestCase
{
    public function testAddPersistsCartRow(): void
    {
        $userId = $this->createUser();
        $bookId = $this->firstBookId();

        $result = $this->cartService()->add($userId, $bookId, 2);
        $this->assertTrue($result['ok']);

        $stmt = $this->db()->prepare(
            'SELECT user_id, book_id, quantity FROM cart_items WHERE id = ?'
        );
        $stmt->execute([$result['item_id']]);
        $row = $stmt->fetch();

        $this->assertNotFalse($row);
        $this->assertSame($userId, (int) $row['user_id']);
        $this->assertSame($bookId, (int) $row['book_id']);
        $this->assertSame(2, (int) $row['quantity']);
    }

    public function testAddSameBookUpdatesQuantityNotDuplicateRow(): void
    {
        $userId = $this->createUser();
        $bookId = $this->firstBookId();
        $cart = $this->cartService();

        $cart->add($userId, $bookId, 1);
        $cart->add($userId, $bookId, 2);

        $stmt = $this->db()->prepare(
            'SELECT COUNT(*) FROM cart_items WHERE user_id = ? AND book_id = ?'
        );
        $stmt->execute([$userId, $bookId]);
        $this->assertSame(1, (int) $stmt->fetchColumn());

        $qty = $this->db()->prepare(
            'SELECT quantity FROM cart_items WHERE user_id = ? AND book_id = ?'
        );
        $qty->execute([$userId, $bookId]);
        $this->assertSame(3, (int) $qty->fetchColumn());
    }

    public function testRemoveDeletesOnlyTargetRow(): void
    {
        $userId = $this->createUser();
        $cart = $this->cartService();
        $first = $this->bookService()->find(1);
        $second = $this->bookService()->find(2);

        $a = $cart->add($userId, (int) $first['id'], 1);
        $cart->add($userId, (int) $second['id'], 1);
        $cart->remove($userId, (int) $a['item_id']);

        $count = $this->db()->prepare('SELECT COUNT(*) FROM cart_items WHERE user_id = ?');
        $count->execute([$userId]);
        $this->assertSame(1, (int) $count->fetchColumn());

        $remaining = $this->db()->prepare(
            'SELECT book_id FROM cart_items WHERE user_id = ?'
        );
        $remaining->execute([$userId]);
        $this->assertSame((int) $second['id'], (int) $remaining->fetchColumn());
    }

    public function testCartRowsAreIsolatedPerUser(): void
    {
        $userA = $this->createUser('a@example.com');
        $userB = $this->createUser('b@example.com');
        $bookId = $this->firstBookId();

        $this->cartService()->add($userA, $bookId, 1);

        $stmt = $this->db()->prepare('SELECT COUNT(*) FROM cart_items WHERE user_id = ?');
        $stmt->execute([$userB]);
        $this->assertSame(0, (int) $stmt->fetchColumn());
    }
}
