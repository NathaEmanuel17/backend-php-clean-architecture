<?php

declare(strict_types=1);

namespace Tests\Unit\User\Application\DTO;

use App\User\Application\DTO\UserOutput;
use PHPUnit\Framework\TestCase;

final class UserOutputTest extends TestCase
{
    public function testShouldCreateUserOutput(): void
    {
        $output = new UserOutput(
            id: '550e8400-e29b-41d4-a716-446655440000',
            name: 'John Doe',
            email: 'john.doe@example.com',
        );

        self::assertSame('550e8400-e29b-41d4-a716-446655440000', $output->id);
        self::assertSame('John Doe', $output->name);
        self::assertSame('john.doe@example.com', $output->email);
    }
}
