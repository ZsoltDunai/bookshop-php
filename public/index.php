<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/src/bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$path = rtrim($path, '/') ?: '/';

if ($method === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    http_response_code(204);
    exit;
}

if ($path === '/health') {
    JsonResponse::json(['status' => 'ok', 'app' => 'bookshop-php']);
}

if (str_starts_with($path, '/api')) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    ApiRouter::dispatch($method, $path);
}

$spaCandidates = [
    __DIR__ . '/browser/index.html',
    __DIR__ . '/index.html',
];
foreach ($spaCandidates as $spaFile) {
    if (is_file($spaFile)) {
        header('Content-Type: text/html; charset=UTF-8');
        readfile($spaFile);
        exit;
    }
}

http_response_code(503);
header('Content-Type: text/plain; charset=UTF-8');
echo "Frontend not built. Run: cd frontend && npm install && npm run build\n";
