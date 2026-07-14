<?php

declare(strict_types=1);

final class HttpResponse
{
    public function __construct(
        public readonly int $status,
        public readonly string $body,
        public readonly array $headers
    ) {
    }

    public function header(string $name): ?string
    {
        $needle = strtolower($name);

        foreach ($this->headers as $key => $value) {
            if (strtolower($key) === $needle) {
                return $value;
            }
        }

        return null;
    }
}
