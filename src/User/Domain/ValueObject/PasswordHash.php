<?php

declare(strict_types=1);

namespace App\User\Domain\ValueObject;

use InvalidArgumentException;

final readonly class PasswordHash
{
    private function __construct(
        private string $value
    ) {
        $info = password_get_info($value);

        if ($info['algo'] !== PASSWORD_ARGON2ID) {
            throw new InvalidArgumentException('Invalid password hash.');
        }
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }
}
