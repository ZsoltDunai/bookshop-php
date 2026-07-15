<?php

declare(strict_types=1);

/**
 * Thin dispatcher: match method + path, call controller action (OCP via route table).
 */
final class ApiRouter
{
    /** @var list<array{0: string, 1: string, 2: callable}> */
    private array $routes;

    public function __construct(
        private readonly BookController $books,
        private readonly AuthController $auth,
        private readonly CartController $cart,
        private readonly OrderController $orders,
    ) {
        $this->routes = [
            ['GET', '#^/api/books$#', fn () => $this->books->index()],
            ['GET', '#^/api/books/(\d+)$#', fn (array $m) => $this->books->show((int) $m[1])],
            ['POST', '#^/api/auth/register$#', fn () => $this->auth->register()],
            ['POST', '#^/api/auth/login$#', fn () => $this->auth->login()],
            ['GET', '#^/api/auth/me$#', fn () => $this->auth->me()],
            ['GET', '#^/api/cart$#', fn () => $this->cart->show()],
            ['POST', '#^/api/cart/items$#', fn () => $this->cart->addItem()],
            ['PATCH', '#^/api/cart/items/(\d+)$#', fn (array $m) => $this->cart->updateItem((int) $m[1])],
            ['DELETE', '#^/api/cart/items/(\d+)$#', fn (array $m) => $this->cart->removeItem((int) $m[1])],
            ['DELETE', '#^/api/cart$#', fn () => $this->cart->clear()],
            ['POST', '#^/api/orders/checkout$#', fn () => $this->orders->checkout()],
            ['GET', '#^/api/orders$#', fn () => $this->orders->index()],
            ['GET', '#^/api/orders/(\d+)$#', fn (array $m) => $this->orders->show((int) $m[1])],
        ];
    }

    public function dispatch(string $method, string $path): void
    {
        foreach ($this->routes as [$routeMethod, $pattern, $handler]) {
            if ($method !== $routeMethod) {
                continue;
            }

            if (!preg_match($pattern, $path, $matches)) {
                continue;
            }

            $handler($matches);
        }

        JsonResponse::error('Not found', 404);
    }
}
