<?php

declare(strict_types=1);

class RoutesTest extends IntegrationTestCase
{
    public function testHomePageLoads(): void
    {
        $response = $this->client()->get('/');

        $this->assertSame(200, $response->status);
        $this->assertStringContainsString('Discover your next great read', $response->body);
        $this->assertStringContainsString('The Great Gatsby', $response->body);
    }

    public function testSearchFiltersBooks(): void
    {
        $response = $this->client()->get('/?q=Orwell');

        $this->assertSame(200, $response->status);
        $this->assertStringContainsString('1984', $response->body);
        $this->assertStringNotContainsString('The Hobbit', $response->body);
    }

    public function testBookDetailPage(): void
    {
        $response = $this->client()->get('/book?id=1');

        $this->assertSame(200, $response->status);
        $this->assertStringContainsString('The Great Gatsby', $response->body);
        $this->assertStringContainsString('F. Scott Fitzgerald', $response->body);
    }

    public function testMissingBookReturns404(): void
    {
        $response = $this->client()->get('/book?id=9999');

        $this->assertSame(404, $response->status);
        $this->assertStringContainsString('404', $response->body);
    }

    public function testUnknownRouteReturns404(): void
    {
        $response = $this->client()->get('/does-not-exist');

        $this->assertSame(404, $response->status);
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
