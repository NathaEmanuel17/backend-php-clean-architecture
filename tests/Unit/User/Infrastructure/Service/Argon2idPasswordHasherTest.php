<?php

declare(strict_types=1);

namespace Tests\Unit\User\Infrastructure\Service;

use App\User\Domain\Service\PasswordHasher;
use App\User\Infrastructure\Service\Argon2idPasswordHasher;
use PHPUnit\Framework\TestCase;

final class Argon2idPasswordHasherTest extends TestCase
{
    public function testShouldHashPlainPasswordUsingArgon2id(): void
    {
        $hasher = new Argon2idPasswordHasher();

        $hash = $hasher->hash('StrongPassword123!');

        self::assertInstanceOf(PasswordHasher::class, $hasher);
        self::assertTrue(password_verify('StrongPassword123!', $hash->value()));

        $info = password_get_info($hash->value());

        self::assertSame(PASSWORD_ARGON2ID, $info['algo']);
    }
}
