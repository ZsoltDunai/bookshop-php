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

        $result = $auth->register('user@example.com', 'anotherpass');

        $this->assertFalse($result['ok']);
        $this->assertSame('An account with this email already exists.', $result['error']);
    }

    public function testRegisterCreatesUser(): void
    {
        $auth = new Auth();
        $result = $auth->register('newuser@example.com', 'password123');

        $this->assertTrue($result['ok']);
        $this->assertSame('newuser@example.com', $result['user']['email']);
        $this->assertGreaterThan(0, $result['user']['id']);
    }

    public function testAuthenticateWithInvalidCredentials(): void
    {
        $this->createUser('user@example.com', 'password123');

        $auth = new Auth();
        $result = $auth->authenticate('user@example.com', 'wrong-password');

        $this->assertFalse($result['ok']);
        $this->assertSame('Invalid credentials', $result['error']);
    }

    public function testAuthenticateWithValidCredentials(): void
    {
        $userId = $this->createUser('user@example.com', 'password123');

        $auth = new Auth();
        $result = $auth->authenticate('user@example.com', 'password123');

        $this->assertTrue($result['ok']);
        $this->assertSame($userId, $result['user_id']);
    }

    public function testFindUserById(): void
    {
        $userId = $this->createUser('user@example.com', 'password123');

        $auth = new Auth();
        $user = $auth->findUserById($userId);

        $this->assertNotNull($user);
        $this->assertSame('user@example.com', $user['email']);
    }
}
