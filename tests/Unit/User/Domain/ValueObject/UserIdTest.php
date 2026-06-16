<?php

declare(strict_types=1);

namespace Tests\Unit\User\Domain\ValueObject;

use App\User\Domain\ValueObject\UserId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class UserIdTest extends TestCase
{
    public function testShouldCreateUserIdFromValidUuid(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';

        $userId = UserId::fromString($uuid);

        self::assertSame($uuid, $userId->value());
    }

    public function testShouldThrowExceptionWhenUuidIsInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        UserId::fromString('invalid-id');
    }
}
