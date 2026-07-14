<?php

declare(strict_types=1);

session_start();

define('ROOT_PATH', dirname(__DIR__));
define('DATA_PATH', ROOT_PATH . '/data');

require_once ROOT_PATH . '/src/Database.php';
require_once ROOT_PATH . '/src/Auth.php';
require_once ROOT_PATH . '/src/BookService.php';
require_once ROOT_PATH . '/src/CartService.php';
require_once ROOT_PATH . '/src/OrderService.php';

Database::initialize();

function view(string $name, array $data = []): void
{
    extract($data);
    $auth = new Auth();
    $currentUser = $auth->user();
    $cartCount = 0;

    if ($currentUser) {
        $cart = new CartService();
        $cartCount = $cart->itemCount((int) $currentUser['id']);
    }

    ob_start();
    require ROOT_PATH . '/views/' . $name . '.php';
    $content = ob_get_clean();
    require ROOT_PATH . '/views/layout.php';
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function formatPrice(float $amount): string
{
    return '$' . number_format($amount, 2);
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
