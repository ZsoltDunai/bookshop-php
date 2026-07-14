<?php

declare(strict_types=1);

class PerformanceTest extends IntegrationTestCase
{
    public function testHealthEndpointRespondsQuickly(): void
    {
        $start = microtime(true);
        $response = $this->client()->get('/health');
        $elapsedMs = (microtime(true) - $start) * 1000;

        $this->assertSame(200, $response->status);
        $this->assertLessThan(500, $elapsedMs, 'Health endpoint took too long.');
    }

    public function testHomePageRespondsQuickly(): void
    {
        $start = microtime(true);
        $response = $this->client()->get('/');
        $elapsedMs = (microtime(true) - $start) * 1000;

        $this->assertSame(200, $response->status);
        $this->assertLessThan(1000, $elapsedMs, 'Home page took too long.');
    }
}
