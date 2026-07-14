<?php

declare(strict_types=1);

class RoutesTest extends IntegrationTestCase
{
    public function testSpaHomeLoads(): void
    {
        $response = $this->client()->get('/');

        $this->assertSame(200, $response->status);
        $this->assertStringContainsString('<app-root>', $response->body);
    }

    public function testBooksApiReturnsCatalog(): void
    {
        $response = $this->client()->getJson('/api/books');
        $payload = json_decode($response->body, true);

        $this->assertSame(200, $response->status);
        $titles = array_column($payload, 'title');
        $this->assertContains('The Great Gatsby', $titles);
    }

    public function testSearchFiltersBooks(): void
    {
        $response = $this->client()->getJson('/api/books?q=Orwell');
        $payload = json_decode($response->body, true);

        $this->assertSame(200, $response->status);
        $this->assertCount(1, $payload);
        $this->assertSame('1984', $payload[0]['title']);
    }

    public function testBookDetailApi(): void
    {
        $response = $this->client()->getJson('/api/books/1');
        $payload = json_decode($response->body, true);

        $this->assertSame(200, $response->status);
        $this->assertSame('The Great Gatsby', $payload['title']);
        $this->assertSame('F. Scott Fitzgerald', $payload['author']);
    }

    public function testMissingBookReturns404(): void
    {
        $response = $this->client()->getJson('/api/books/9999');
        $payload = json_decode($response->body, true);

        $this->assertSame(404, $response->status);
        $this->assertSame('Book not found', $payload['detail']);
    }

    public function testUnknownApiRouteReturns404(): void
    {
        $response = $this->client()->getJson('/api/does-not-exist');
        $payload = json_decode($response->body, true);

        $this->assertSame(404, $response->status);
        $this->assertSame('Not found', $payload['detail']);
    }

    public function testHealthEndpointReturnsJson(): void
    {
        $response = $this->client()->get('/health');
        $payload = json_decode($response->body, true);

        $this->assertSame(200, $response->status);
        $this->assertSame('ok', $payload['status']);
        $this->assertSame('bookshop-php', $payload['app']);
    }
}
