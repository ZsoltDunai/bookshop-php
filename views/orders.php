<?php $pageTitle = 'Orders'; ?>

<section class="page-header">
    <h1>Order history</h1>
</section>

<?php if (empty($orderDetails)): ?>
    <div class="empty-state">
        <p>You haven't placed any orders yet.</p>
        <a href="/" class="btn btn-primary">Start shopping</a>
    </div>
<?php else: ?>
    <div class="orders-list">
        <?php foreach ($orderDetails as $detail): ?>
            <?php $order = $detail['order']; ?>
            <article class="order-card">
                <header class="order-header">
                    <div>
                        <h2>Order #<?= (int) $order['id'] ?></h2>
                        <time datetime="<?= e($order['created_at']) ?>">
                            <?= e(date('M j, Y g:i A', strtotime($order['created_at']))) ?>
                        </time>
                    </div>
                    <div class="order-total">
                        <?= formatPrice((float) $order['total']) ?>
                    </div>
                </header>

                <ul class="order-items">
                    <?php foreach ($detail['items'] as $item): ?>
                        <li>
                            <span class="order-item-title"><?= e($item['title']) ?></span>
                            <span class="order-item-qty">×<?= (int) $item['quantity'] ?></span>
                            <span class="order-item-price"><?= formatPrice((float) $item['price'] * (int) $item['quantity']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
