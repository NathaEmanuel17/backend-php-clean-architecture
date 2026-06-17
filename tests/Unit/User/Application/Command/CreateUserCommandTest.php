<?php

declare(strict_types=1);

namespace Tests\Unit\User\Application\Command;

use App\User\Application\Command\CreateUserCommand;
use PHPUnit\Framework\TestCase;

final class CreateUserCommandTest extends TestCase
{
    public function testShouldCreateCommand(): void
    {
        $command = new CreateUserCommand(
            name: 'John Doe',
            email: 'john.doe@example.com',
            plainPassword: 'StrongPassword123!',
        );

        self::assertSame('John Doe', $command->name);
        self::assertSame('john.doe@example.com', $command->email);
        self::assertSame('StrongPassword123!', $command->plainPassword);
    }
}
