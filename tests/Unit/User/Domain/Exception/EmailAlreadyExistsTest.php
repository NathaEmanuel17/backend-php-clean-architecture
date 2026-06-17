<?php

declare(strict_types=1);

namespace Tests\Unit\User\Domain\Exception;

use App\User\Domain\Exception\EmailAlreadyExists;
use PHPUnit\Framework\TestCase;

final class EmailAlreadyExistsTest extends TestCase
{
    public function testShouldCreateExceptionWithDefaultMessage(): void
    {
        $exception = new EmailAlreadyExists();

        self::assertSame('Email already exists.', $exception->getMessage());
    }
}
