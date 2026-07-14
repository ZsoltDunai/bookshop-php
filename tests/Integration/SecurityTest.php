<?php

declare(strict_types=1);

class SecurityTest extends IntegrationTestCase
{
    public function testSearchEscapesHtml(): void
    {
        $payload = '<script>alert(1)</script>';
        $response = $this->client()->get('/?q=' . urlencode($payload));

        $this->assertSame(200, $response->status);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $response->body);
        $this->assertStringContainsString(htmlspecialchars($payload, ENT_QUOTES, 'UTF-8'), $response->body);
    }

    public function testPasswordHashNotExposedInHtml(): void
    {
        $this->login();

        $response = $this->client()->get('/');

        $this->assertStringNotContainsString('$2y$', $response->body);
        $this->assertStringNotContainsString('password_hash', $response->body);
    }

    public function testCartIsolationBetweenUsers(): void
    {
        $userA = $this->newClient();
        $userB = $this->newClient();

        $userA->post('/login', [
            'email' => 'demo@bookshop.io',
            'password' => 'password123',
        ]);
        $userA->post('/cart/add', [
            'book_id' => 1,
            'quantity' => 1,
            'redirect' => '/cart',
        ]);

        $cartA = $userA->get('/cart');
        $this->assertStringContainsString('The Great Gatsby', $cartA->body);

        $email = 'isolated-' . uniqid() . '@bookshop.io';
        $userB->post('/register', [
            'email' => $email,
            'password' => 'password123',
        ]);

        $cartB = $userB->get('/cart');
        $this->assertStringContainsString('Your cart is empty', $cartB->body);
    }

    public function testOrdersOnlyVisibleToOwner(): void
    {
        $owner = $this->newClient();
        $other = $this->newClient();

        $owner->post('/login', [
            'email' => 'demo@bookshop.io',
            'password' => 'password123',
        ]);
        $owner->post('/cart/add', ['book_id' => 1, 'quantity' => 1, 'redirect' => '/cart']);
        $owner->post('/checkout');

        $ownerOrders = $owner->get('/orders');
        $this->assertStringContainsString('Order #', $ownerOrders->body);

        $email = 'other-' . uniqid() . '@bookshop.io';
        $other->post('/register', ['email' => $email, 'password' => 'password123']);

        $otherOrders = $other->get('/orders');
        $this->assertStringContainsString("You haven't placed any orders yet", $otherOrders->body);
    }

    public function testSqlInjectionInSearchDoesNotBreakApp(): void
    {
        $response = $this->client()->get("/?q=' OR 1=1 --");

        $this->assertSame(200, $response->status);
        $this->assertStringContainsString('No books found', $response->body);
    }
}
