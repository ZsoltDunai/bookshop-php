<?php $pageTitle = 'Login'; ?>

<section class="auth-card">
    <h1 data-testid="login-heading">Welcome back</h1>
    <p class="auth-sub">Sign in to browse, cart, and checkout.</p>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error" data-testid="login-alert"><?= e($error) ?></div>
    <?php endif; ?>

    <form action="/login" method="post" class="auth-form">
        <label>
            Email
            <input type="email" name="email" value="<?= e($email ?? '') ?>" required autofocus data-testid="login-email">
        </label>
        <label>
            Password
            <input type="password" name="password" required data-testid="login-password">
        </label>
        <button type="submit" class="btn btn-primary btn-block" data-testid="login-submit">Sign in</button>
    </form>

    <p class="auth-footer">
        Don't have an account? <a href="/register">Create one</a>
    </p>

    <div class="demo-box">
        <strong>Demo account</strong>
        <p>demo@bookshop.io / password123</p>
    </div>
</section>
