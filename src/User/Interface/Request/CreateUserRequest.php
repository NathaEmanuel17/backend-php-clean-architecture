<?php

declare(strict_types=1);

namespace App\User\Interface\Request;

use InvalidArgumentException;

final readonly class CreateUserRequest
{
    private function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        if (!isset($payload['name']) || !is_string($payload['name'])) {
            throw new InvalidArgumentException('Name is required.');
        }

        if (!isset($payload['email']) || !is_string($payload['email'])) {
            throw new InvalidArgumentException('Email is required.');
        }

        if (!isset($payload['password']) || !is_string($payload['password'])) {
            throw new InvalidArgumentException('Password is required.');
        }

        return new self(
            name: $payload['name'],
            email: $payload['email'],
            password: $payload['password'],
        );
    }
}
