<?php $pageTitle = 'Browse'; ?>

<section class="hero">
    <h1 data-testid="home-heading">Discover your next great read</h1>
    <p class="hero-sub">Curated classics and modern favorites, delivered to your door.</p>

    <form action="/" method="get" class="search-form">
        <input
            type="search"
            name="q"
            placeholder="Search by title or author…"
            value="<?= e($searchQuery) ?>"
            class="search-input"
            data-testid="search-input"
        >
        <button type="submit" class="btn btn-primary" data-testid="search-submit">Search</button>
    </form>
</section>

<?php if (empty($books)): ?>
    <div class="empty-state" data-testid="search-empty">
        <p>No books found<?= $searchQuery !== '' ? ' for "' . e($searchQuery) . '"' : '' ?>.</p>
        <?php if ($searchQuery !== ''): ?>
            <a href="/" class="btn btn-ghost">Clear search</a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="book-grid" data-testid="book-grid">
        <?php foreach ($books as $book): ?>
            <article class="book-card" data-testid="book-card" data-book-id="<?= (int) $book['id'] ?>">
                <div class="book-cover">
                    <span class="book-cover-letter"><?= e(strtoupper($book['title'][0])) ?></span>
                </div>
                <div class="book-info">
                    <h2 class="book-title">
                        <a href="/book?id=<?= (int) $book['id'] ?>"><?= e($book['title']) ?></a>
                    </h2>
                    <p class="book-author"><?= e($book['author']) ?></p>
                    <div class="book-meta">
                        <span class="book-price"><?= formatPrice((float) $book['price']) ?></span>
                        <span class="book-stock <?= (int) $book['stock'] < 3 ? 'low' : '' ?>">
                            <?= (int) $book['stock'] ?> in stock
                        </span>
                    </div>
                    <?php if ($currentUser && (int) $book['stock'] > 0): ?>
                        <form action="/cart/add" method="post" class="add-form">
                            <input type="hidden" name="book_id" value="<?= (int) $book['id'] ?>">
                            <input type="hidden" name="redirect" value="/">
                            <button type="submit" class="btn btn-secondary btn-block" data-testid="add-to-cart">Add to cart</button>
                        </form>
                    <?php elseif (!$currentUser): ?>
                        <a href="/login" class="btn btn-ghost btn-block" data-testid="login-to-buy">Login to buy</a>
                    <?php else: ?>
                        <button class="btn btn-ghost btn-block" disabled>Out of stock</button>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
