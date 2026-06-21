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

    public function put(
        string $path,
        Closure $handler
    ): void {
        $this->routes['PUT ' . $path] = $handler;
    }

    public function delete(
        string $path,
        Closure $handler
    ): void {
        $this->routes['DELETE ' . $path] = $handler;
    }

    public function dispatch(Request $request): JsonResponse|ProblemJsonResponse
    {
        $exactKey = $request->method() . ' ' . $request->path();

        if (isset($this->routes[$exactKey])) {
            return $this->execute(
                $this->routes[$exactKey],
                $request
            );
        }

        foreach ($this->routes as $routeKey => $handler) {
            [$method, $routePath] = explode(' ', $routeKey, 2);

            if ($method !== $request->method()) {
                continue;
            }

            $params = $this->matchRoute(
                $routePath,
                $request->path()
            );

            if ($params === null) {
                continue;
            }

            return $this->execute(
                $handler,
                $request->withParams($params)
            );
        }

        throw new InvalidArgumentException('Route not found.');
    }

    /**
     * @return array<string, string>|null
     */
    private function matchRoute(string $routePath, string $requestPath): ?array
    {
        $routeParts = explode('/', trim($routePath, '/'));
        $requestParts = explode('/', trim($requestPath, '/'));

        if (count($routeParts) !== count($requestParts)) {
            return null;
        }

        $params = [];

        foreach ($routeParts as $index => $routePart) {
            $requestPart = $requestParts[$index];

            if (
                str_starts_with($routePart, '{')
                && str_ends_with($routePart, '}')
            ) {
                $paramName = trim($routePart, '{}');

                $params[$paramName] = $requestPart;

                continue;
            }

            if ($routePart !== $requestPart) {
                return null;
            }
        }

        return $params;
    }

    private function execute(
        Closure $handler,
        Request $request
    ): JsonResponse|ProblemJsonResponse {
        $response = $handler($request);

        if (
            !$response instanceof JsonResponse
            && !$response instanceof ProblemJsonResponse
        ) {
            throw new InvalidArgumentException('Invalid route response.');
        }

        return $response;
    }
}
