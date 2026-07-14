<?php $pageTitle = 'Browse'; ?>

<section class="hero">
    <h1>Discover your next great read</h1>
    <p class="hero-sub">Curated classics and modern favorites, delivered to your door.</p>

    <form action="/" method="get" class="search-form">
        <input
            type="search"
            name="q"
            placeholder="Search by title or author…"
            value="<?= e($searchQuery) ?>"
            class="search-input"
        >
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
</section>

<?php if (empty($books)): ?>
    <div class="empty-state">
        <p>No books found<?= $searchQuery !== '' ? ' for "' . e($searchQuery) . '"' : '' ?>.</p>
        <?php if ($searchQuery !== ''): ?>
            <a href="/" class="btn btn-ghost">Clear search</a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="book-grid">
        <?php foreach ($books as $book): ?>
            <article class="book-card">
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
                            <button type="submit" class="btn btn-secondary btn-block">Add to cart</button>
                        </form>
                    <?php elseif (!$currentUser): ?>
                        <a href="/login" class="btn btn-ghost btn-block">Login to buy</a>
                    <?php else: ?>
                        <button class="btn btn-ghost btn-block" disabled>Out of stock</button>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
