<?php

declare(strict_types=1);

/**
 * Composition root — wires dependencies once (DIP without a container).
 */
final class App
{
    private Auth $auth;
    private BookService $books;
    private CartService $cart;
    private OrderService $orders;
    private ApiRouter $router;

    public function __construct(?PDO $db = null)
    {
        $pdo = $db ?? Database::getInstance();

        $this->auth = new Auth($pdo);
        $this->books = new BookService($pdo);
        $this->cart = new CartService($pdo, $this->books);
        $this->orders = new OrderService($pdo, $this->cart);
        $this->router = new ApiRouter(
            new BookController($this->books),
            new AuthController($this->auth),
            new CartController($this->cart),
            new OrderController($this->orders),
        );
    }

    public function router(): ApiRouter
    {
        return $this->router;
    }

    public function auth(): Auth
    {
        return $this->auth;
    }

    public function books(): BookService
    {
        return $this->books;
    }

    public function cart(): CartService
    {
        return $this->cart;
    }

    public function orders(): OrderService
    {
        return $this->orders;
    }
}
