<?php

declare(strict_types=1);

namespace Tests\Unit\User\Application\Command;

use App\User\Application\Command\UpdateUserCommand;
use PHPUnit\Framework\TestCase;

final class UpdateUserCommandTest extends TestCase
{
    public function testShouldCreateCommand(): void
    {
        $command = new UpdateUserCommand(
            id: '550e8400-e29b-41d4-a716-446655440000',
            name: 'Jane Doe',
            email: 'jane.doe@example.com',
            plainPassword: 'NewStrongPassword123!',
        );

        self::assertSame('550e8400-e29b-41d4-a716-446655440000', $command->id);
        self::assertSame('Jane Doe', $command->name);
        self::assertSame('jane.doe@example.com', $command->email);
        self::assertSame('NewStrongPassword123!', $command->plainPassword);
    }
}
