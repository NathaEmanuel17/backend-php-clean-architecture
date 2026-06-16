<?php

declare(strict_types=1);

namespace App\User\Domain\ValueObject;

use InvalidArgumentException;

final readonly class UserName
{
    private const int MIN_LENGTH = 2;
    private const int MAX_LENGTH = 120;

    private function __construct(
        private string $value
    ) {
        $length = mb_strlen($value);

        if ($length < self::MIN_LENGTH) {
            throw new InvalidArgumentException('User name is too short.');
        }

        if ($length > self::MAX_LENGTH) {
            throw new InvalidArgumentException('User name is too long.');
        }
    }

    public static function fromString(string $value): self
    {
        return new self(trim($value));
    }

    public function value(): string
    {
        return $this->value;
    }
}
