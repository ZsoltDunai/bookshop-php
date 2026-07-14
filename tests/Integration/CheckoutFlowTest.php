<?php

declare(strict_types=1);

class CheckoutFlowTest extends IntegrationTestCase
{
    public function testFullPurchaseFlow(): void
    {
        $this->login();

        $add = $this->client()->postJson('/api/cart/items', [
            'book_id' => 1,
            'quantity' => 1,
        ]);
        $this->assertSame(201, $add->status);

        $cart = $this->client()->getJson('/api/cart');
        $cartPayload = json_decode($cart->body, true);
        $this->assertCount(1, $cartPayload['items']);
        $this->assertSame('The Great Gatsby', $cartPayload['items'][0]['book']['title']);

        $checkout = $this->client()->postJson('/api/orders/checkout');
        $this->assertSame(201, $checkout->status);

        $orders = $this->client()->getJson('/api/orders');
        $ordersPayload = json_decode($orders->body, true);
        $this->assertCount(1, $ordersPayload);
        $this->assertSame('The Great Gatsby', $ordersPayload[0]['items'][0]['book']['title']);
    }

    public function testUpdateCartQuantity(): void
    {
        $this->login();
        $add = $this->client()->postJson('/api/cart/items', [
            'book_id' => 1,
            'quantity' => 1,
        ]);
        $this->assertSame(201, $add->status);
        $item = json_decode($add->body, true);

        $update = $this->client()->patchJson('/api/cart/items/' . $item['id'], [
            'quantity' => 2,
        ]);
        $this->assertSame(200, $update->status);

        $updatedCart = $this->client()->getJson('/api/cart');
        $payload = json_decode($updatedCart->body, true);
        $this->assertSame(2, $payload['items'][0]['quantity']);
    }

    public function testCheckoutEmptyCartShowsError(): void
    {
        $this->login();

        $checkout = $this->client()->postJson('/api/orders/checkout');
        $this->assertSame(400, $checkout->status);
        $payload = json_decode($checkout->body, true);
        $this->assertStringContainsString('empty', strtolower($payload['detail']));
    }
}
