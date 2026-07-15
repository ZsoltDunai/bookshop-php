<?php

declare(strict_types=1);

class BooksApiTest extends IntegrationTestCase
{
    public function testListBooksReturnsArrayOfBooks(): void
    {
        $response = $this->client()->getJson('/api/books');
        $payload = json_decode($response->body, true);

        $this->assertSame(200, $response->status);
        $this->assertIsArray($payload);
        $this->assertGreaterThanOrEqual(8, count($payload));
        $this->assertArrayHasKey('id', $payload[0]);
        $this->assertArrayHasKey('title', $payload[0]);
        $this->assertArrayHasKey('author', $payload[0]);
        $this->assertArrayHasKey('price', $payload[0]);
        $this->assertArrayHasKey('stock', $payload[0]);
    }

    public function testSearchBooksFiltersByQuery(): void
    {
        $response = $this->client()->getJson('/api/books?q=Orwell');
        $payload = json_decode($response->body, true);

        $this->assertSame(200, $response->status);
        $this->assertCount(1, $payload);
        $this->assertSame('1984', $payload[0]['title']);
    }

    public function testGetBookById(): void
    {
        $response = $this->client()->getJson('/api/books/1');
        $payload = json_decode($response->body, true);

        $this->assertSame(200, $response->status);
        $this->assertSame(1, $payload['id']);
        $this->assertSame('The Great Gatsby', $payload['title']);
    }

    public function testGetMissingBookReturns404(): void
    {
        $response = $this->client()->getJson('/api/books/99999');
        $payload = json_decode($response->body, true);

        $this->assertSame(404, $response->status);
        $this->assertSame('Book not found', $payload['detail']);
    }
}
