<?php

declare(strict_types=1);

final class AuthContext
{
    public static function userId(): ?int
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        if (!preg_match('/^Bearer\s+(.+)$/i', $header, $matches)) {
            return null;
        }

        return JwtAuth::validateToken(trim($matches[1]));
    }

    public static function requireUserId(): int
    {
        $userId = self::userId();
        if ($userId === null) {
            header('WWW-Authenticate: Bearer');
            JsonResponse::error('Could not validate credentials', 401);
        }

        return $userId;
    }
}
