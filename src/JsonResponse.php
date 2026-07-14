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

    public static function readJsonBody(): array
    {
        $raw = file_get_contents('php://input');
        if ($raw === false || trim($raw) === '') {
            return [];
        }

        $data = json_decode($raw, true);
        if (!is_array($data)) {
            self::error('Invalid JSON body', 400);
        }

        return $data;
    }

    public static function bearerUserId(): ?int
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        if (!preg_match('/^Bearer\s+(.+)$/i', $header, $matches)) {
            return null;
        }

        return JwtAuth::validateToken(trim($matches[1]));
    }

    public static function requireUserId(): int
    {
        $userId = self::bearerUserId();
        if ($userId === null) {
            header('WWW-Authenticate: Bearer');
            self::error('Could not validate credentials', 401);
        }

        return $userId;
    }
}
