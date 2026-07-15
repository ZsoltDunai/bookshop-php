<?php

declare(strict_types=1);

final class JsonResponse
{
    public static function json(mixed $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_THROW_ON_ERROR);
        exit;
    }

    public static function error(string $detail, int $status = 400): never
    {
        self::json(['detail' => $detail], $status);
    }

    public static function noContent(): never
    {
        http_response_code(204);
        exit;
    }

    public static function fromResult(array $result, int $successStatus = 200): never
    {
        if ($result['ok'] ?? false) {
            self::json($result['data'] ?? null, $successStatus);
        }

        self::error(
            (string) ($result['error'] ?? 'Request failed'),
            self::statusForCode($result['code'] ?? null)
        );
    }

    public static function statusForCode(?string $code): int
    {
        return match ($code) {
            'not_found' => 404,
            'unauthorized' => 401,
            'conflict' => 409,
            default => 400,
        };
    }
}
