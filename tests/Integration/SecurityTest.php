<?php

declare(strict_types=1);

class SecurityTest extends IntegrationTestCase
{
    public function testSearchWithSqlInjectionReturnsEmpty(): void
    {
        $response = $this->client()->getJson('/api/books?q=' . urlencode("' OR 1=1 --"));
        $payload = json_decode($response->body, true);

        $this->assertSame(200, $response->status);
        $this->assertCount(0, $payload);
    }

    public function testPasswordHashNotExposedInApi(): void
    {
        $this->login();

        $response = $this->client()->getJson('/api/auth/me');
        $payload = json_decode($response->body, true);

        $this->assertArrayHasKey('email', $payload);
        $this->assertArrayNotHasKey('password_hash', $payload);
        $this->assertStringNotContainsString('$2y$', $response->body);
    }

    public function testCartIsolationBetweenUsers(): void
    {
        $userA = $this->newClient();
        $userB = $this->newClient();

        $loginA = $userA->postJson('/api/auth/login', [
            'email' => 'demo@bookshop.io',
            'password' => 'password123',
        ]);
        $tokenA = json_decode($loginA->body, true)['access_token'];
        $userA->setToken($tokenA);

        $userA->postJson('/api/cart/items', ['book_id' => 1, 'quantity' => 1]);
        $cartA = $userA->getJson('/api/cart');
        $this->assertStringContainsString('The Great Gatsby', $cartA->body);

        $email = 'isolated-' . uniqid() . '@bookshop.io';
        $registerB = $userB->postJson('/api/auth/register', [
            'email' => $email,
            'password' => 'password123',
        ]);
        $loginB = $userB->postJson('/api/auth/login', [
            'email' => $email,
            'password' => 'password123',
        ]);
        $userB->setToken(json_decode($loginB->body, true)['access_token']);

        $cartB = $userB->getJson('/api/cart');
        $payload = json_decode($cartB->body, true);
        $this->assertCount(0, $payload['items']);
    }

    public function testOrdersOnlyVisibleToOwner(): void
    {
        $owner = $this->newClient();
        $other = $this->newClient();

        $ownerLogin = $owner->postJson('/api/auth/login', [
            'email' => 'demo@bookshop.io',
            'password' => 'password123',
        ]);
        $owner->setToken(json_decode($ownerLogin->body, true)['access_token']);
        $owner->postJson('/api/cart/items', ['book_id' => 1, 'quantity' => 1]);
        $owner->postJson('/api/orders/checkout');

        $ownerOrders = $owner->getJson('/api/orders');
        $this->assertGreaterThan(0, count(json_decode($ownerOrders->body, true)));

        $email = 'other-' . uniqid() . '@bookshop.io';
        $other->postJson('/api/auth/register', ['email' => $email, 'password' => 'password123']);
        $otherLogin = $other->postJson('/api/auth/login', ['email' => $email, 'password' => 'password123']);
        $other->setToken(json_decode($otherLogin->body, true)['access_token']);

        $otherOrders = $other->getJson('/api/orders');
        $this->assertCount(0, json_decode($otherOrders->body, true));
    }

    public function testProtectedCartEndpointRequiresToken(): void
    {
        $response = $this->client()->getJson('/api/cart');
        $this->assertSame(401, $response->status);
    }
}
