<?php

declare(strict_types=1);

namespace Tests\Unit\Shared\Interface\Http;

use App\Shared\Interface\Http\Request;
use PHPUnit\Framework\TestCase;

final class RequestTest extends TestCase
{
    public function testShouldCreateRequest(): void
    {
        $request = new Request(
            method: 'POST',
            path: '/users',
            body: [
                'name' => 'John Doe',
            ],
        );

        self::assertSame('POST', $request->method());
        self::assertSame('/users', $request->path());
        self::assertSame(
            ['name' => 'John Doe'],
            $request->body()
        );
    }
}
