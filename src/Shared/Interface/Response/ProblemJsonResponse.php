<?php

declare(strict_types=1);

namespace App\Shared\Interface\Response;

final readonly class ProblemJsonResponse
{
    public function __construct(
        private string $type,
        private string $title,
        private int $statusCode,
        private string $detail,
    ) {
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    public function content(): string
    {
        return json_encode(
            [
                'type' => $this->type,
                'title' => $this->title,
                'status' => $this->statusCode,
                'detail' => $this->detail,
            ],
            JSON_THROW_ON_ERROR
        );
    }

    /**
     * @return array<string, string>
     */
    public function headers(): array
    {
        return [
            'Content-Type' => 'application/problem+json',
        ];
    }
}
