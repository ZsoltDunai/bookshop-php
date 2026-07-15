<?php

declare(strict_types=1);

final class Cors
{
    public static function apply(): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS');
    }

    public static function handlePreflight(): void
    {
        self::apply();
        http_response_code(204);
        exit;
    }
}
