<?php

declare(strict_types=1);

namespace Tests\Unit\Shared\Interface\Response;

use App\Shared\Interface\Response\JsonResponse;
use PHPUnit\Framework\TestCase;

final class JsonResponseTest extends TestCase
{
    public function testShouldCreateJsonResponse(): void
    {
        $response = new JsonResponse(
            data: ['message' => 'ok'],
            statusCode: 200,
        );

        self::assertSame(200, $response->statusCode());
        self::assertSame('{"message":"ok"}', $response->content());
        self::assertSame(
            ['Content-Type' => 'application/json'],
            $response->headers()
        );
    }
}
