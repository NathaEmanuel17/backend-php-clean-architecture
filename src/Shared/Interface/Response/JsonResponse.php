<?php

declare(strict_types=1);

namespace App\Shared\Interface\Response;

final readonly class JsonResponse
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        private array $data,
        private int $statusCode = 200,
    ) {
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    public function content(): string
    {
        return json_encode(
            $this->data,
            JSON_THROW_ON_ERROR
        );
    }

    /**
     * @return array<string, string>
     */
    public function headers(): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }
}
