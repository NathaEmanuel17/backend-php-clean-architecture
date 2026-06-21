<?php

declare(strict_types=1);

namespace App\Shared\Interface\Http;

final readonly class Request
{
    /**
     * @param array<string, mixed> $body
     * @param array<string, string> $params
     */
    public function __construct(
        private string $method,
        private string $path,
        private array $body = [],
        private array $params = [],
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

    public function param(string $name): ?string
    {
        return $this->params[$name] ?? null;
    }

    /**
     * @param array<string, string> $params
     */
    public function withParams(array $params): self
    {
        return new self(
            method: $this->method,
            path: $this->path,
            body: $this->body,
            params: $params,
        );
    }
}
