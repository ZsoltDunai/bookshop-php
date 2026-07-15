<?php

declare(strict_types=1);

final class Request
{
    public static function jsonBody(): array
    {
        $raw = file_get_contents('php://input');
        if ($raw === false || trim($raw) === '') {
            return [];
        }

        $data = json_decode($raw, true);
        if (!is_array($data)) {
            JsonResponse::error('Invalid JSON body', 400);
        }

        return $data;
    }

    public static function query(string $key, string $default = ''): string
    {
        return trim((string) ($_GET[$key] ?? $default));
    }
}
