<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Bookshop') ?> — Bookshop</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a href="/" class="logo">
                <span class="logo-icon">📚</span>
                <span class="logo-text">Bookshop</span>
            </a>

            <nav class="nav">
                <a href="/" class="nav-link">Browse</a>
                <?php if ($currentUser): ?>
                    <a href="/cart" class="nav-link">
                        Cart
                        <?php if ($cartCount > 0): ?>
                            <span class="badge"><?= (int) $cartCount ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="/orders" class="nav-link">Orders</a>
                    <span class="nav-user"><?= e($currentUser['email']) ?></span>
                    <form action="/logout" method="post" class="inline-form">
                        <button type="submit" class="btn btn-ghost btn-sm">Logout</button>
                    </form>
                <?php else: ?>
                    <a href="/login" class="nav-link">Login</a>
                    <a href="/register" class="btn btn-primary btn-sm">Sign up</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="main">
        <div class="container">
            <?php $flash = getFlash(); ?>
            <?php if ($flash): ?>
                <div class="alert alert-<?= e($flash['type']) ?>">
                    <?= e($flash['message']) ?>
                </div>
            <?php endif; ?>

            <?= $content ?>
        </div>
    </main>

    <footer class="site-footer">
        <div class="container">
            <p>Bookshop Demo — A simple PHP application</p>
            <?php if (!$currentUser): ?>
                <p class="demo-hint">Demo account: <strong>demo@bookshop.io</strong> / <strong>password123</strong></p>
            <?php endif; ?>
        </div>
    </footer>
</body>
</html>
