<?php

declare(strict_types=1);

final class TestServer
{
    private static ?resource $process = null;
    private static ?int $port = null;
    private static ?string $dbPath = null;
    private static ?string $logPath = null;

    public static function start(): void
    {
        if (self::$process !== null) {
            return;
        }

        self::$port = self::findFreePort();
        self::$dbPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'bookshop-int-' . uniqid('', true) . '.sqlite';
        self::$logPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'bookshop-int-' . uniqid('', true) . '.log';

        $command = sprintf(
            '%s -S 127.0.0.1:%d -t %s %s',
            escapeshellarg(PHP_BINARY),
            self::$port,
            escapeshellarg(ROOT_PATH . '/public'),
            escapeshellarg(ROOT_PATH . '/router.php')
        );

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['file', self::$logPath, 'a'],
            2 => ['file', self::$logPath, 'a'],
        ];

        self::$process = proc_open(
            $command,
            $descriptors,
            $pipes,
            ROOT_PATH,
            ['BOOKSHOP_DB' => 'sqlite:' . self::$dbPath]
        );

        if (!is_resource(self::$process)) {
            throw new RuntimeException('Failed to start test server.');
        }

        self::waitForHealth();
    }

    public static function stop(): void
    {
        if (is_resource(self::$process)) {
            proc_terminate(self::$process);
            proc_close(self::$process);
            self::$process = null;
        }

        if (self::$dbPath !== null && file_exists(self::$dbPath)) {
            unlink(self::$dbPath);
            self::$dbPath = null;
        }

        if (self::$logPath !== null && file_exists(self::$logPath)) {
            unlink(self::$logPath);
            self::$logPath = null;
        }

        self::$port = null;
        Database::reset();
        putenv('BOOKSHOP_DB');
    }

    public static function baseUrl(): string
    {
        if (self::$port === null) {
            throw new RuntimeException('Test server is not running.');
        }

        return 'http://127.0.0.1:' . self::$port;
    }

    public static function logPath(): ?string
    {
        return self::$logPath;
    }

    private static function waitForHealth(): void
    {
        $client = new HttpClient(self::baseUrl(), tempnam(sys_get_temp_dir(), 'bookshop-health-'));

        for ($attempt = 0; $attempt < 30; $attempt++) {
            try {
                $response = $client->get('/health');
                if ($response->status === 200) {
                    return;
                }
            } catch (Throwable) {
            }

            usleep(200_000);
        }

        $log = self::$logPath !== null && file_exists(self::$logPath)
            ? file_get_contents(self::$logPath)
            : 'No log available.';

        throw new RuntimeException("Test server failed to become healthy.\n" . $log);
    }

    private static function findFreePort(): int
    {
        $server = stream_socket_server('tcp://127.0.0.1:0', $errno, $errstr);
        if ($server === false) {
            return 18080 + random_int(1, 1000);
        }

        $address = stream_socket_get_name($server, false);
        fclose($server);

        if ($address === false || !str_contains($address, ':')) {
            return 18080 + random_int(1, 1000);
        }

        return (int) substr($address, strrpos($address, ':') + 1);
    }
}
