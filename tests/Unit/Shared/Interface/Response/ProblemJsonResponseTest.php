<?php

declare(strict_types=1);

namespace Tests\Unit\Shared\Interface\Response;

use App\Shared\Interface\Response\ProblemJsonResponse;
use PHPUnit\Framework\TestCase;

final class ProblemJsonResponseTest extends TestCase
{
    public function testShouldCreateProblemJsonResponse(): void
    {
        $response = new ProblemJsonResponse(
            type: 'https://example.com/errors/validation',
            title: 'Validation failed',
            statusCode: 422,
            detail: 'Invalid email.',
        );

        self::assertSame(422, $response->statusCode());

        self::assertSame(
            '{"type":"https:\/\/example.com\/errors\/validation","title":"Validation failed","status":422,"detail":"Invalid email."}',
            $response->content()
        );

        self::assertSame(
            ['Content-Type' => 'application/problem+json'],
            $response->headers()
        );
    }
}
