<?php

declare(strict_types=1);

namespace Tests\Unit\Shared\Interface\Http;

use App\Shared\Interface\Http\Request;
use App\Shared\Interface\Http\Router;
use App\Shared\Interface\Response\JsonResponse;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    public function testShouldDispatchExactRoute(): void
    {
        $router = new Router();

        $router->post('/users', static fn (Request $request): JsonResponse => new JsonResponse([
            'message' => 'created',
        ]));

        $response = $router->dispatch(
            new Request(
                method: 'POST',
                path: '/users',
                body: [],
            )
        );

        self::assertSame(200, $response->statusCode());
        self::assertSame('{"message":"created"}', $response->content());
    }
}
