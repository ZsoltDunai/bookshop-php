<?php $pageTitle = 'Register'; ?>

<section class="auth-card">
    <h1>Create account</h1>
    <p class="auth-sub">Join Bookshop and start building your library.</p>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form action="/register" method="post" class="auth-form">
        <label>
            Email
            <input type="email" name="email" value="<?= e($email ?? '') ?>" required autofocus>
        </label>
        <label>
            Password
            <input type="password" name="password" minlength="6" required>
        </label>
        <button type="submit" class="btn btn-primary btn-block">Create account</button>
    </form>

    <p class="auth-footer">
        Already have an account? <a href="/login">Sign in</a>
    </p>
</section>
