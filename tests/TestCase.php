<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

abstract class TestCase extends PHPUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->resetTestDatabase();
        $this->resetSession();
    }

    protected function tearDown(): void
    {
        $this->resetTestDatabase();
        $this->resetSession();
        parent::tearDown();
    }

    protected function resetTestDatabase(): void
    {
        Database::reset();
        putenv('BOOKSHOP_DB=sqlite::memory:');
        Database::initialize();
    }

    protected function resetSession(): void
    {
        $_SESSION = [];

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    protected function db(): PDO
    {
        return Database::getInstance();
    }

    protected function authService(): Auth
    {
        return new Auth($this->db());
    }

    protected function bookService(): BookService
    {
        return new BookService($this->db());
    }

    protected function cartService(): CartService
    {
        return new CartService($this->db(), $this->bookService());
    }

    protected function orderService(): OrderService
    {
        return new OrderService($this->db(), $this->cartService());
    }

    protected function createUser(string $email = 'test@example.com', string $password = 'password123'): int
    {
        $stmt = $this->db()->prepare('INSERT INTO users (email, password_hash) VALUES (?, ?)');
        $stmt->execute([$email, password_hash($password, PASSWORD_DEFAULT)]);

        return (int) $this->db()->lastInsertId();
    }

    protected function loginAs(int $userId): void
    {
        $_SESSION['user_id'] = $userId;
    }

    protected function firstBookId(): int
    {
        return (int) $this->bookService()->all()[0]['id'];
    }
}
