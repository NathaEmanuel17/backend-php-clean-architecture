<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Service;

use App\User\Domain\Service\PasswordHasher;
use App\User\Domain\ValueObject\PasswordHash;

final readonly class Argon2idPasswordHasher implements PasswordHasher
{
    public function hash(string $plainPassword): PasswordHash
    {
        return PasswordHash::fromString(
            password_hash($plainPassword, PASSWORD_ARGON2ID)
        );
    }
}
