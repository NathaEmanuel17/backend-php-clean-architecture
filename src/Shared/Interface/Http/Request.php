<?php

declare(strict_types=1);

namespace App\Shared\Interface\Http;

final readonly class Request
{
    /**
     * @param array<string, mixed> $body
     */
    public function __construct(
        private string $method,
        private string $path,
        private array $body = [],
    ) {
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    /**
     * @return array<string, mixed>
     */
    public function body(): array
    {
        return $this->body;
    }
}
