<?php

declare(strict_types=1);

namespace App\Shared\Interface\Http;

use App\Shared\Interface\Response\JsonResponse;
use App\Shared\Interface\Response\ProblemJsonResponse;
use Closure;
use InvalidArgumentException;

final class Router
{
    /**
     * @var array<string, Closure>
     */
    private array $routes = [];

    public function post(string $path, Closure $handler): void
    {
        $this->routes['POST ' . $path] = $handler;
    }

    public function get(
        string $path,
        Closure $handler
    ): void {
        $this->routes['GET ' . $path] = $handler;
    }

    public function dispatch(Request $request): JsonResponse|ProblemJsonResponse
    {
        $key = $request->method() . ' ' . $request->path();

        if (!isset($this->routes[$key])) {
            throw new InvalidArgumentException('Route not found.');
        }

        $response = $this->routes[$key]($request);

        if (
            !$response instanceof JsonResponse
            && !$response instanceof ProblemJsonResponse
        ) {
            throw new InvalidArgumentException('Invalid route response.');
        }

        return $response;
    }
}
