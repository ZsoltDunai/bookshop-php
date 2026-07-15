<?php

declare(strict_types=1);

/**
 * Shared request validation helpers for the HTTP edge.
 */
final class RequestValidator
{
    public static function fail(string $error): array
    {
        return ['ok' => false, 'error' => $error, 'code' => 'validation'];
    }

    public static function ok(array $data = []): array
    {
        return ['ok' => true, 'data' => $data];
    }

    public static function requireString(array $body, string $key): ?string
    {
        if (!array_key_exists($key, $body) || !is_string($body[$key])) {
            return null;
        }

        return trim($body[$key]);
    }

    public static function requirePositiveInt(array $body, string $key): ?int
    {
        if (!array_key_exists($key, $body)) {
            return null;
        }

        if (is_int($body[$key])) {
            return $body[$key] > 0 ? $body[$key] : null;
        }

        if (is_string($body[$key]) && ctype_digit($body[$key])) {
            $value = (int) $body[$key];

            return $value > 0 ? $value : null;
        }

        if (is_float($body[$key]) && floor($body[$key]) === $body[$key]) {
            $value = (int) $body[$key];

            return $value > 0 ? $value : null;
        }

        return null;
    }
}
