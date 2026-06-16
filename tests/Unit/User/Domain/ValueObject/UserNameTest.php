<?php

declare(strict_types=1);

namespace Tests\Unit\User\Domain\ValueObject;

use App\User\Domain\ValueObject\UserName;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class UserNameTest extends TestCase
{
    public function testShouldCreateUserNameFromValidValue(): void
    {
        $name = UserName::fromString('John Doe');

        self::assertSame('John Doe', $name->value());
    }

    public function testShouldTrimUserName(): void
    {
        $name = UserName::fromString('  John Doe  ');

        self::assertSame('John Doe', $name->value());
    }

    public function testShouldThrowExceptionWhenUserNameIsTooShort(): void
    {
        $this->expectException(InvalidArgumentException::class);

        UserName::fromString('J');
    }

    public function testShouldThrowExceptionWhenUserNameIsTooLong(): void
    {
        $this->expectException(InvalidArgumentException::class);

        UserName::fromString(str_repeat('A', 121));
    }
}
