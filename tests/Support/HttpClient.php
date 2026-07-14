<?php

declare(strict_types=1);

final class HttpClient
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $cookieJar
    ) {
    }

    public function get(string $path, bool $followRedirects = true): HttpResponse
    {
        return $this->request('GET', $path, [], $followRedirects);
    }

    public function post(string $path, array $data = [], bool $followRedirects = true): HttpResponse
    {
        return $this->request('POST', $path, $data, $followRedirects);
    }

    public function request(string $method, string $path, array $data = [], bool $followRedirects = true): HttpResponse
    {
        if (!function_exists('curl_init')) {
            throw new RuntimeException('curl extension is required for integration tests.');
        }

        $ch = curl_init($this->baseUrl . $path);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_COOKIEJAR => $this->cookieJar,
            CURLOPT_COOKIEFILE => $this->cookieJar,
            CURLOPT_FOLLOWLOCATION => $followRedirects,
            CURLOPT_CUSTOMREQUEST => $method,
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        $raw = curl_exec($ch);
        if ($raw === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new RuntimeException('HTTP request failed: ' . $error);
        }

        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);

        $rawHeaders = substr($raw, 0, $headerSize);
        $body = substr($raw, $headerSize);
        $headers = $this->parseHeaders($rawHeaders);

        return new HttpResponse($status, $body, $headers);
    }

    private function parseHeaders(string $rawHeaders): array
    {
        $headers = [];

        foreach (explode("\r\n", $rawHeaders) as $line) {
            if (!str_contains($line, ':')) {
                continue;
            }

            [$name, $value] = explode(':', $line, 2);
            $headers[trim($name)] = trim($value);
        }

        return $headers;
    }
}
