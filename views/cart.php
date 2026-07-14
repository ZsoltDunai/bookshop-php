<?php $pageTitle = 'Cart'; ?>

<section class="page-header">
    <h1 data-testid="cart-heading">Your cart</h1>
</section>

<?php if (empty($items)): ?>
    <div class="empty-state" data-testid="cart-empty">
        <p>Your cart is empty.</p>
        <a href="/" class="btn btn-primary">Browse books</a>
    </div>
<?php else: ?>
    <div class="cart-layout" data-testid="cart-layout">
        <div class="cart-items">
            <?php foreach ($items as $item): ?>
                <div class="cart-item" data-testid="cart-item" data-item-id="<?= (int) $item['id'] ?>">
                    <div class="cart-item-cover">
                        <span><?= e(strtoupper($item['title'][0])) ?></span>
                    </div>
                    <div class="cart-item-info">
                        <h3><?= e($item['title']) ?></h3>
                        <p class="cart-item-author"><?= e($item['author']) ?></p>
                        <p class="cart-item-price"><?= formatPrice((float) $item['price']) ?> each</p>
                    </div>
                    <div class="cart-item-actions">
                        <form action="/cart/update" method="post" class="qty-form">
                            <input type="hidden" name="item_id" value="<?= (int) $item['id'] ?>">
                            <input type="number" name="quantity" value="<?= (int) $item['quantity'] ?>" min="1" max="<?= (int) $item['stock'] ?>" class="qty-input" data-testid="cart-qty">
                            <button type="submit" class="btn btn-ghost btn-sm" data-testid="cart-update">Update</button>
                        </form>
                        <form action="/cart/remove" method="post">
                            <input type="hidden" name="item_id" value="<?= (int) $item['id'] ?>">
                            <button type="submit" class="btn btn-ghost btn-sm text-danger" data-testid="cart-remove">Remove</button>
                        </form>
                    </div>
                    <div class="cart-item-total">
                        <?= formatPrice((float) $item['price'] * (int) $item['quantity']) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <aside class="cart-summary" data-testid="cart-summary">
            <h2>Order summary</h2>
            <div class="summary-row">
                <span>Subtotal</span>
                <span><?= formatPrice($total) ?></span>
            </div>
            <div class="summary-row">
                <span>Shipping</span>
                <span>Free</span>
            </div>
            <div class="summary-row total" data-testid="cart-total">
                <span>Total</span>
                <span><?= formatPrice($total) ?></span>
            </div>
            <form action="/checkout" method="post">
                <button type="submit" class="btn btn-primary btn-block" data-testid="checkout-btn">Checkout</button>
            </form>
            <a href="/" class="btn btn-ghost btn-block">Continue shopping</a>
        </aside>
    </div>
<?php endif; ?>
