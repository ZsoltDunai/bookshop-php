<?php

declare(strict_types=1);

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');

function serveStaticFile(string $file): never
{
    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $mimeTypes = [
        'js' => 'application/javascript',
        'css' => 'text/css',
        'map' => 'application/json',
        'html' => 'text/html',
        'json' => 'application/json',
        'ico' => 'image/x-icon',
        'png' => 'image/png',
        'svg' => 'image/svg+xml',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
    ];

    header('Content-Type: ' . ($mimeTypes[$extension] ?? 'application/octet-stream'));
    readfile($file);
    exit;
}

if ($uri !== '/' && $uri !== '/index.php') {
    $candidates = [
        __DIR__ . '/public' . $uri,
        __DIR__ . '/public/browser' . $uri,
    ];

    foreach ($candidates as $file) {
        if (is_file($file)) {
            serveStaticFile($file);
        }
    }
}

require_once __DIR__ . '/public/index.php';
