<?php $pageTitle = $book['title']; ?>

<nav class="breadcrumb">
    <a href="/">Browse</a>
    <span>/</span>
    <span><?= e($book['title']) ?></span>
</nav>

<article class="book-detail">
    <div class="book-detail-cover">
        <span class="book-cover-letter large"><?= e(strtoupper($book['title'][0])) ?></span>
    </div>

    <div class="book-detail-info">
        <h1><?= e($book['title']) ?></h1>
        <p class="book-detail-author">by <?= e($book['author']) ?></p>
        <p class="book-detail-price"><?= formatPrice((float) $book['price']) ?></p>

        <p class="book-detail-stock <?= (int) $book['stock'] < 3 ? 'low' : '' ?>">
            <?= (int) $book['stock'] ?> copies available
        </p>

        <?php if (!empty($book['description'])): ?>
            <p class="book-detail-desc"><?= e($book['description']) ?></p>
        <?php endif; ?>

        <?php if ($currentUser && (int) $book['stock'] > 0): ?>
            <form action="/cart/add" method="post" class="add-form detail-form">
                <input type="hidden" name="book_id" value="<?= (int) $book['id'] ?>">
                <input type="hidden" name="redirect" value="/book?id=<?= (int) $book['id'] ?>">
                <label class="qty-label">
                    Quantity
                    <input type="number" name="quantity" value="1" min="1" max="<?= (int) $book['stock'] ?>" class="qty-input">
                </label>
                <button type="submit" class="btn btn-primary">Add to cart</button>
            </form>
        <?php elseif (!$currentUser): ?>
            <a href="/login" class="btn btn-primary">Login to purchase</a>
        <?php else: ?>
            <button class="btn btn-ghost" disabled>Currently out of stock</button>
        <?php endif; ?>
    </div>
</article>
