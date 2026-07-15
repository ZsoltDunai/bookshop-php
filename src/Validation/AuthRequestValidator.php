<?php

declare(strict_types=1);

final class AuthRequestValidator
{
    public static function register(array $body): array
    {
        $email = RequestValidator::requireString($body, 'email');
        $password = RequestValidator::requireString($body, 'password');

        if ($email === null || $email === '') {
            return RequestValidator::fail('Email is required.');
        }

        if ($password === null || $password === '') {
            return RequestValidator::fail('Password is required.');
        }

        return RequestValidator::ok([
            'email' => $email,
            'password' => $password,
        ]);
    }

    public static function login(array $body): array
    {
        return self::register($body);
    }
}
