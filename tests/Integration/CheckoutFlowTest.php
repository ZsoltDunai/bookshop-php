<?php

declare(strict_types=1);

class CheckoutFlowTest extends IntegrationTestCase
{
    public function testFullPurchaseFlow(): void
    {
        $this->login();

        $add = $this->client()->post('/cart/add', [
            'book_id' => 1,
            'quantity' => 1,
            'redirect' => '/cart',
        ], false);
        $this->assertSame(302, $add->status);

        $cart = $this->client()->get('/cart');
        $this->assertStringContainsString('The Great Gatsby', $cart->body);
        $this->assertStringContainsString('Checkout', $cart->body);

        $checkout = $this->client()->post('/checkout', [], false);
        $this->assertSame(302, $checkout->status);

        $orders = $this->client()->get('/orders');
        $this->assertStringContainsString('Order #', $orders->body);
        $this->assertStringContainsString('The Great Gatsby', $orders->body);
    }

    public function testUpdateCartQuantity(): void
    {
        $this->login();
        $this->client()->post('/cart/add', [
            'book_id' => 1,
            'quantity' => 1,
            'redirect' => '/cart',
        ]);

        $cart = $this->client()->get('/cart');
        preg_match('/data-item-id="(\d+)"/', $cart->body, $matches);
        $this->assertNotEmpty($matches);

        $update = $this->client()->post('/cart/update', [
            'item_id' => $matches[1],
            'quantity' => 2,
        ], false);
        $this->assertSame(302, $update->status);

        $updatedCart = $this->client()->get('/cart');
        $this->assertStringContainsString('value="2"', $updatedCart->body);
    }

    public function testCheckoutEmptyCartShowsError(): void
    {
        $this->login();

        $checkout = $this->client()->post('/checkout', [], false);
        $this->assertSame(302, $checkout->status);

        $cart = $this->client()->get('/cart');
        $this->assertStringContainsString('Your cart is empty', $cart->body);
    }
}
