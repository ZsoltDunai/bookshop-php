<?php

declare(strict_types=1);

class AuthRoutesTest extends IntegrationTestCase
{
    public function testCartRequiresAuthentication(): void
    {
        $response = $this->client()->getJson('/api/cart');

        $this->assertSame(401, $response->status);
        $payload = json_decode($response->body, true);
        $this->assertSame('Could not validate credentials', $payload['detail']);
    }

    public function testInvalidLoginReturns401(): void
    {
        $response = $this->client()->postJson('/api/auth/login', [
            'email' => 'demo@bookshop.io',
            'password' => 'wrong-password',
        ]);

        $this->assertSame(401, $response->status);
        $payload = json_decode($response->body, true);
        $this->assertSame('Invalid credentials', $payload['detail']);
    }

    public function testValidLoginReturnsToken(): void
    {
        $response = $this->client()->postJson('/api/auth/login', [
            'email' => 'demo@bookshop.io',
            'password' => 'password123',
        ]);

        $this->assertSame(200, $response->status);
        $payload = json_decode($response->body, true);
        $this->assertArrayHasKey('access_token', $payload);
        $this->assertSame('bearer', $payload['token_type']);
    }

    public function testRegisterCreatesAccount(): void
    {
        $email = 'new-user-' . uniqid() . '@bookshop.io';
        $response = $this->client()->postJson('/api/auth/register', [
            'email' => $email,
            'password' => 'password123',
        ]);

        $this->assertSame(201, $response->status);
        $payload = json_decode($response->body, true);
        $this->assertSame($email, $payload['email']);
    }

    public function testMeReturnsCurrentUser(): void
    {
        $this->login();

        $response = $this->client()->getJson('/api/auth/me');

        $this->assertSame(200, $response->status);
        $payload = json_decode($response->body, true);
        $this->assertSame('demo@bookshop.io', $payload['email']);
    }
}
