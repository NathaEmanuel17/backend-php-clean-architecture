<?php

declare(strict_types=1);

namespace Tests\Unit\User\Domain\Service;

use App\User\Domain\Service\PasswordHasher;
use App\User\Domain\ValueObject\PasswordHash;
use PHPUnit\Framework\TestCase;

final class PasswordHasherTest extends TestCase
{
    public function testShouldHashPlainPassword(): void
    {
        $hasher = new class () implements PasswordHasher {
            public function hash(string $plainPassword): PasswordHash
            {
                return PasswordHash::fromString(password_hash($plainPassword, PASSWORD_ARGON2ID));
            }
        };

        $hash = $hasher->hash('StrongPassword123!');

        self::assertInstanceOf(PasswordHash::class, $hash);
    }
}
