<?php

declare(strict_types=1);

namespace Tests\Unit\User\Domain\ValueObject;

use App\User\Domain\ValueObject\PasswordHash;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PasswordHashTest extends TestCase
{
    public function testShouldCreatePasswordHashFromValidHash(): void
    {
        $hash = password_hash('StrongPassword123!', PASSWORD_ARGON2ID);

        $passwordHash = PasswordHash::fromString($hash);

        self::assertSame($hash, $passwordHash->value());
    }

    public function testShouldThrowExceptionWhenHashIsInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PasswordHash::fromString('plain-password');
    }
}
