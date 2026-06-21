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

    public function testShouldDispatchGetRoute(): void
    {
        $router = new Router();

        $router->get(
            '/users',
            static fn (Request $request): JsonResponse =>
            new JsonResponse([
                'message' => 'ok',
            ])
        );

        $response = $router->dispatch(
            new Request(
                method: 'GET',
                path: '/users',
            )
        );

        self::assertSame(
            '{"message":"ok"}',
            $response->content()
        );
    }

    public function testShouldDispatchPutRoute(): void
    {
        $router = new Router();

        $router->put(
            '/users',
            static fn (Request $request): JsonResponse =>
                new JsonResponse(['message' => 'updated'])
        );

        $response = $router->dispatch(
            new Request(
                method: 'PUT',
                path: '/users',
            )
        );

        self::assertSame(
            '{"message":"updated"}',
            $response->content()
        );
    }

    public function testShouldDispatchDeleteRoute(): void
    {
        $router = new Router();

        $router->delete(
            '/users',
            static fn (Request $request): JsonResponse =>
                new JsonResponse(['message' => 'deleted'])
        );

        $response = $router->dispatch(
            new Request(
                method: 'DELETE',
                path: '/users',
            )
        );

        self::assertSame(
            '{"message":"deleted"}',
            $response->content()
        );
    }
}
