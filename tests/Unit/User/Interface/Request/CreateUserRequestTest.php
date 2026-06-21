<?php

declare(strict_types=1);

namespace Tests\Unit\User\Interface\Request;

use App\User\Interface\Request\CreateUserRequest;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CreateUserRequestTest extends TestCase
{
    public function testShouldCreateRequestFromValidPayload(): void
    {
        $request = CreateUserRequest::fromArray([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'StrongPassword123!',
        ]);

        self::assertSame('John Doe', $request->name);
        self::assertSame('john.doe@example.com', $request->email);
        self::assertSame('StrongPassword123!', $request->password);
    }

    public function testShouldThrowExceptionWhenNameIsMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);

        CreateUserRequest::fromArray([
            'email' => 'john.doe@example.com',
            'password' => 'StrongPassword123!',
        ]);
    }
}
