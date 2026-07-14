<?php

declare(strict_types=1);

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');

if ($uri !== '/' && $uri !== '/index.php') {
    $candidates = [
        __DIR__ . '/public' . $uri,
        __DIR__ . '/public/browser' . $uri,
    ];

    foreach ($candidates as $file) {
        if (is_file($file)) {
            return false;
        }
    }
}

require_once __DIR__ . '/public/index.php';
