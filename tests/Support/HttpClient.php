<?php

declare(strict_types=1);

final class HttpClient
{
    private ?string $bearerToken = null;

    public function __construct(
        private readonly string $baseUrl,
        private readonly string $cookieJar
    ) {
    }

    public function setToken(?string $token): void
    {
        $this->bearerToken = $token;
    }

    public function get(string $path, bool $followRedirects = true): HttpResponse
    {
        return $this->request('GET', $path, [], $followRedirects);
    }

    public function post(string $path, array $data = [], bool $followRedirects = true): HttpResponse
    {
        return $this->request('POST', $path, $data, $followRedirects);
    }

    public function patch(string $path, array $data = []): HttpResponse
    {
        return $this->request('PATCH', $path, $data, false);
    }

    public function delete(string $path): HttpResponse
    {
        return $this->request('DELETE', $path, [], false);
    }

    public function postJson(string $path, array $data = []): HttpResponse
    {
        return $this->requestJson('POST', $path, $data);
    }

    public function getJson(string $path): HttpResponse
    {
        return $this->requestJson('GET', $path);
    }

    public function patchJson(string $path, array $data): HttpResponse
    {
        return $this->requestJson('PATCH', $path, $data);
    }

    public function deleteJson(string $path): HttpResponse
    {
        return $this->requestJson('DELETE', $path);
    }

    public function request(string $method, string $path, array $data = [], bool $followRedirects = true): HttpResponse
    {
        if (!function_exists('curl_init')) {
            throw new RuntimeException('curl extension is required for integration tests.');
        }

        $ch = curl_init($this->baseUrl . $path);
        $headers = $this->authHeaders();

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_COOKIEJAR => $this->cookieJar,
            CURLOPT_COOKIEFILE => $this->cookieJar,
            CURLOPT_FOLLOWLOCATION => $followRedirects,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        return $this->execute($ch);
    }

    private function requestJson(string $method, string $path, ?array $data = null): HttpResponse
    {
        if (!function_exists('curl_init')) {
            throw new RuntimeException('curl extension is required for integration tests.');
        }

        $ch = curl_init($this->baseUrl . $path);
        $headers = array_merge(['Content-Type: application/json'], $this->authHeaders());

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_COOKIEJAR => $this->cookieJar,
            CURLOPT_COOKIEFILE => $this->cookieJar,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_THROW_ON_ERROR));
        }

        return $this->execute($ch);
    }

    private function authHeaders(): array
    {
        return $this->bearerToken !== null
            ? ['Authorization: Bearer ' . $this->bearerToken]
            : [];
    }

    private function execute(mixed $ch): HttpResponse
    {
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
