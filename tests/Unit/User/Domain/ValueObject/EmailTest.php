<?php

declare(strict_types=1);

namespace Tests\Unit\User\Domain\ValueObject;

use App\User\Domain\ValueObject\Email;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class EmailTest extends TestCase
{
    public function testShouldCreateEmailFromValidValue(): void
    {
        $email = Email::fromString('john.doe@example.com');

        self::assertSame('john.doe@example.com', $email->value());
    }

    public function testShouldNormalizeEmailToLowercase(): void
    {
        $email = Email::fromString('JOHN.DOE@EXAMPLE.COM');

        self::assertSame('john.doe@example.com', $email->value());
    }

    public function testShouldThrowExceptionWhenEmailIsInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Email::fromString('invalid-email');
    }
}
