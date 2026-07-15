<?php

declare(strict_types=1);

class AuthRequestValidatorTest extends TestCase
{
    public function testRegisterRequiresEmail(): void
    {
        $result = AuthRequestValidator::register(['password' => 'password123']);

        $this->assertFalse($result['ok']);
        $this->assertSame('Email is required.', $result['error']);
        $this->assertSame('validation', $result['code']);
    }

    public function testRegisterRequiresPassword(): void
    {
        $result = AuthRequestValidator::register(['email' => 'user@example.com']);

        $this->assertFalse($result['ok']);
        $this->assertSame('Password is required.', $result['error']);
    }

    public function testRegisterAcceptsValidPayload(): void
    {
        $result = AuthRequestValidator::register([
            'email' => '  user@example.com ',
            'password' => 'password123',
        ]);

        $this->assertTrue($result['ok']);
        $this->assertSame('user@example.com', $result['data']['email']);
        $this->assertSame('password123', $result['data']['password']);
    }
}
