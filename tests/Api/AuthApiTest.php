<?php

declare(strict_types=1);

/**
 * HTTP API tests — request validation and response contracts.
 */
class AuthApiTest extends IntegrationTestCase
{
    public function testRegisterRejectsMissingEmail(): void
    {
        $response = $this->client()->postJson('/api/auth/register', [
            'password' => 'password123',
        ]);

        $this->assertSame(400, $response->status);
        $payload = json_decode($response->body, true);
        $this->assertSame('Email is required.', $payload['detail']);
    }

    public function testRegisterRejectsInvalidEmail(): void
    {
        $response = $this->client()->postJson('/api/auth/register', [
            'email' => 'not-an-email',
            'password' => 'password123',
        ]);

        $this->assertSame(400, $response->status);
        $payload = json_decode($response->body, true);
        $this->assertSame('Please enter a valid email address.', $payload['detail']);
    }

    public function testRegisterRejectsShortPassword(): void
    {
        $response = $this->client()->postJson('/api/auth/register', [
            'email' => 'short-' . uniqid() . '@bookshop.io',
            'password' => '123',
        ]);

        $this->assertSame(400, $response->status);
        $payload = json_decode($response->body, true);
        $this->assertSame('Password must be at least 6 characters.', $payload['detail']);
    }

    public function testRegisterRejectsDuplicateEmail(): void
    {
        $email = 'dup-api-' . uniqid() . '@bookshop.io';
        $this->client()->postJson('/api/auth/register', [
            'email' => $email,
            'password' => 'password123',
        ]);

        $response = $this->client()->postJson('/api/auth/register', [
            'email' => $email,
            'password' => 'password123',
        ]);

        $this->assertSame(409, $response->status);
        $payload = json_decode($response->body, true);
        $this->assertSame('An account with this email already exists.', $payload['detail']);
    }

    public function testLoginRejectsMissingPassword(): void
    {
        $response = $this->client()->postJson('/api/auth/login', [
            'email' => 'demo@bookshop.io',
        ]);

        $this->assertSame(400, $response->status);
        $payload = json_decode($response->body, true);
        $this->assertSame('Password is required.', $payload['detail']);
    }

    public function testLoginReturnsBearerTokenShape(): void
    {
        $response = $this->client()->postJson('/api/auth/login', [
            'email' => 'demo@bookshop.io',
            'password' => 'password123',
        ]);

        $this->assertSame(200, $response->status);
        $payload = json_decode($response->body, true);
        $this->assertIsString($payload['access_token']);
        $this->assertNotSame('', $payload['access_token']);
        $this->assertSame('bearer', $payload['token_type']);
    }
}
