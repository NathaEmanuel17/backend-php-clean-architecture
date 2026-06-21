<?php

declare(strict_types=1);

namespace Tests\Integration\Shared\Interface\Http;

use App\Shared\Interface\Http\Request;
use App\Shared\Interface\Http\Router;
use App\Shared\Interface\Response\JsonResponse;
use PHPUnit\Framework\TestCase;

final class RouterIntegrationTest extends TestCase
{
    public function testShouldDispatchUserRouteWithIdParameter(): void
    {
        $router = new Router();

        $router->get(
            '/users/{id}',
            static fn (Request $request): JsonResponse => new JsonResponse([
                'id' => $request->param('id'),
            ])
        );

        $response = $router->dispatch(
            new Request(
                method: 'GET',
                path: '/users/550e8400-e29b-41d4-a716-446655440000',
            )
        );

        self::assertSame(200, $response->statusCode());
        self::assertSame(
            '{"id":"550e8400-e29b-41d4-a716-446655440000"}',
            $response->content()
        );
    }
}
