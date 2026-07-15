<?php

declare(strict_types=1);

/**
 * HTTP API tests for cart validation and happy path payloads.
 */
class CartApiTest extends IntegrationTestCase
{
    public function testAddItemRejectsMissingBookId(): void
    {
        $this->login();

        $response = $this->client()->postJson('/api/cart/items', [
            'quantity' => 1,
        ]);

        $this->assertSame(400, $response->status);
        $payload = json_decode($response->body, true);
        $this->assertSame('book_id must be a positive integer.', $payload['detail']);
    }

    public function testAddItemRejectsInvalidQuantity(): void
    {
        $this->login();

        $response = $this->client()->postJson('/api/cart/items', [
            'book_id' => 1,
            'quantity' => 0,
        ]);

        $this->assertSame(400, $response->status);
        $payload = json_decode($response->body, true);
        $this->assertSame('quantity must be a positive integer.', $payload['detail']);
    }

    public function testAddItemRejectsUnknownBook(): void
    {
        $this->login();

        $response = $this->client()->postJson('/api/cart/items', [
            'book_id' => 99999,
            'quantity' => 1,
        ]);

        $this->assertSame(404, $response->status);
        $payload = json_decode($response->body, true);
        $this->assertSame('Book not found.', $payload['detail']);
    }

    public function testAddItemReturnsCartItemPayload(): void
    {
        $email = 'cart-api-' . uniqid() . '@bookshop.io';
        $this->registerUser($email);
        $this->login($email);

        $response = $this->client()->postJson('/api/cart/items', [
            'book_id' => 1,
            'quantity' => 1,
        ]);

        $this->assertSame(201, $response->status);
        $payload = json_decode($response->body, true);
        $this->assertSame(1, $payload['book_id']);
        $this->assertSame(1, $payload['quantity']);
        $this->assertSame('The Great Gatsby', $payload['book']['title']);
        $this->assertArrayHasKey('price', $payload['book']);
    }

    public function testUpdateItemRequiresQuantity(): void
    {
        $email = 'cart-upd-' . uniqid() . '@bookshop.io';
        $this->registerUser($email);
        $this->login($email);

        $add = $this->client()->postJson('/api/cart/items', ['book_id' => 1, 'quantity' => 1]);
        $itemId = json_decode($add->body, true)['id'];

        $response = $this->client()->patchJson('/api/cart/items/' . $itemId, []);

        $this->assertSame(400, $response->status);
        $payload = json_decode($response->body, true);
        $this->assertSame('quantity is required.', $payload['detail']);
    }
}
