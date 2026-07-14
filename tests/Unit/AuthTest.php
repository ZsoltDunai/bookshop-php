<?php

declare(strict_types=1);

class AuthTest extends TestCase
{
    public function testRegisterWithInvalidEmail(): void
    {
        $auth = new Auth();
        $result = $auth->register('not-an-email', 'password123');

        $this->assertFalse($result['ok']);
        $this->assertSame('Please enter a valid email address.', $result['error']);
    }

    public function testRegisterWithShortPassword(): void
    {
        $auth = new Auth();
        $result = $auth->register('user@example.com', '123');

        $this->assertFalse($result['ok']);
        $this->assertSame('Password must be at least 6 characters.', $result['error']);
    }

    public function testRegisterDuplicateEmail(): void
    {
        $auth = new Auth();
        $auth->register('user@example.com', 'password123');

        $this->resetSession();

        $result = $auth->register('user@example.com', 'anotherpass');

        $this->assertFalse($result['ok']);
        $this->assertSame('An account with this email already exists.', $result['error']);
    }

    public function testRegisterCreatesSession(): void
    {
        $auth = new Auth();
        $result = $auth->register('newuser@example.com', 'password123');

        $this->assertTrue($result['ok']);
        $this->assertNotNull($auth->user());
        $this->assertSame('newuser@example.com', $auth->user()['email']);
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $this->createUser('user@example.com', 'password123');

        $auth = new Auth();
        $result = $auth->login('user@example.com', 'wrong-password');

        $this->assertFalse($result['ok']);
        $this->assertSame('Invalid email or password.', $result['error']);
        $this->assertNull($auth->user());
    }

    public function testLoginWithValidCredentials(): void
    {
        $userId = $this->createUser('user@example.com', 'password123');

        $auth = new Auth();
        $result = $auth->login('user@example.com', 'password123');

        $this->assertTrue($result['ok']);
        $this->assertSame($userId, $auth->user()['id']);
    }

    public function testLogoutClearsSession(): void
    {
        $userId = $this->createUser('user@example.com', 'password123');
        $this->loginAs($userId);

        $auth = new Auth();
        $this->assertNotNull($auth->user());

        $auth->logout();

        $this->assertNull($auth->user());
    }
}
