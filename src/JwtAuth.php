<?php

declare(strict_types=1);

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

final class JwtAuth
{
    private const ALGORITHM = 'HS256';
    private const TTL_SECONDS = 86400;

    private static function secret(): string
    {
        return getenv('BOOKSHOP_JWT_SECRET') ?: 'bookshop-demo-secret-change-in-production';
    }

    public static function createToken(int $userId): string
    {
        $payload = [
            'sub' => (string) $userId,
            'iat' => time(),
            'exp' => time() + self::TTL_SECONDS,
        ];

        return JWT::encode($payload, self::secret(), self::ALGORITHM);
    }

    public static function validateToken(string $token): ?int
    {
        try {
            $decoded = JWT::decode($token, new Key(self::secret(), self::ALGORITHM));
            $userId = $decoded->sub ?? null;

            return $userId !== null ? (int) $userId : null;
        } catch (Throwable) {
            return null;
        }
    }
}
