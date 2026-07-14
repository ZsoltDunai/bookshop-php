<?php $pageTitle = 'Register'; ?>

<section class="auth-card">
    <h1 data-testid="register-heading">Create account</h1>
    <p class="auth-sub">Join Bookshop and start building your library.</p>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error" data-testid="register-alert"><?= e($error) ?></div>
    <?php endif; ?>

    <form action="/register" method="post" class="auth-form">
        <label>
            Email
            <input type="email" name="email" value="<?= e($email ?? '') ?>" required autofocus data-testid="register-email">
        </label>
        <label>
            Password
            <input type="password" name="password" minlength="6" required data-testid="register-password">
        </label>
        <button type="submit" class="btn btn-primary btn-block" data-testid="register-submit">Create account</button>
    </form>

    <p class="auth-footer">
        Already have an account? <a href="/login">Sign in</a>
    </p>
</section>
