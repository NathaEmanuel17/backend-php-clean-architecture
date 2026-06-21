<?php

declare(strict_types=1);

namespace Tests\Unit\User\Domain\Exception;

use App\User\Domain\Exception\UserNotFound;
use PHPUnit\Framework\TestCase;

final class UserNotFoundTest extends TestCase
{
    public function testShouldCreateExceptionWithDefaultMessage(): void
    {
        $exception = new UserNotFound();

        self::assertSame('User not found.', $exception->getMessage());
    }
}
