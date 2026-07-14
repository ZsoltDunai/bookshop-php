<?php

declare(strict_types=1);

class AuthRoutesTest extends IntegrationTestCase
{
    public function testCartRedirectsToLoginWhenUnauthenticated(): void
    {
        $response = $this->client()->get('/cart', false);

        $this->assertSame(302, $response->status);
        $this->assertStringContainsString('/login', $response->header('Location') ?? '');
    }

    public function testLoginPageLoads(): void
    {
        $response = $this->client()->get('/login');

        $this->assertSame(200, $response->status);
        $this->assertStringContainsString('Welcome back', $response->body);
    }

    public function testInvalidLoginShowsError(): void
    {
        $response = $this->client()->post('/login', [
            'email' => 'demo@bookshop.io',
            'password' => 'wrong-password',
        ]);

        $this->assertSame(200, $response->status);
        $this->assertStringContainsString('Invalid email or password', $response->body);
    }

    public function testValidLoginRedirectsHome(): void
    {
        $response = $this->client()->post('/login', [
            'email' => 'demo@bookshop.io',
            'password' => 'password123',
        ], false);

        $this->assertSame(302, $response->status);
        $this->assertStringContainsString('/', $response->header('Location') ?? '');

        $home = $this->client()->get('/');
        $this->assertStringContainsString('demo@bookshop.io', $home->body);
    }

    public function testRegisterCreatesAccount(): void
    {
        $email = 'new-user-' . uniqid() . '@bookshop.io';
        $response = $this->client()->post('/register', [
            'email' => $email,
            'password' => 'password123',
        ], false);

        $this->assertSame(302, $response->status);

        $home = $this->client()->get('/');
        $this->assertStringContainsString($email, $home->body);
    }

    public function testLogoutClearsSession(): void
    {
        $this->login();

        $logout = $this->client()->post('/logout', [], false);
        $this->assertSame(302, $logout->status);

        $cart = $this->client()->get('/cart', false);
        $this->assertSame(302, $cart->status);
    }
}
