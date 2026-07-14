<?php

declare(strict_types=1);

abstract class IntegrationTestCase extends TestCase
{
    protected static HttpClient $client;
    private static string $cookieJar;
    private static int $serverUsers = 0;

    public static function setUpBeforeClass(): void
    {
        if (self::$serverUsers === 0) {
            TestServer::start();
        }

        self::$serverUsers++;
        self::$cookieJar = tempnam(sys_get_temp_dir(), 'bookshop-cookie-');
        self::$client = new HttpClient(TestServer::baseUrl(), self::$cookieJar);
    }

    public static function tearDownAfterClass(): void
    {
        self::$serverUsers--;

        if (self::$serverUsers === 0) {
            TestServer::stop();
        }

        if (isset(self::$cookieJar) && file_exists(self::$cookieJar)) {
            unlink(self::$cookieJar);
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (file_exists(self::$cookieJar)) {
            unlink(self::$cookieJar);
        }

        touch(self::$cookieJar);
        self::$client = new HttpClient(TestServer::baseUrl(), self::$cookieJar);
    }

    protected function client(): HttpClient
    {
        return self::$client;
    }

    protected function login(string $email = 'demo@bookshop.io', string $password = 'password123'): void
    {
        $response = $this->client()->postJson('/api/auth/login', [
            'email' => $email,
            'password' => $password,
        ]);

        $this->assertSame(200, $response->status, $response->body);
        $payload = json_decode($response->body, true);
        $this->assertArrayHasKey('access_token', $payload);
        $this->client()->setToken($payload['access_token']);
    }

    protected function registerUser(string $email, string $password = 'password123'): void
    {
        $response = $this->client()->postJson('/api/auth/register', [
            'email' => $email,
            'password' => $password,
        ]);

        $this->assertSame(201, $response->status, $response->body);
    }

    protected function newClient(): HttpClient
    {
        return new HttpClient(TestServer::baseUrl(), tempnam(sys_get_temp_dir(), 'bookshop-client-'));
    }
}
