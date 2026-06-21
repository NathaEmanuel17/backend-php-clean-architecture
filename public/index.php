<?php

declare(strict_types=1);

use App\Shared\Interface\Http\Container;
use App\Shared\Interface\Http\Request;
use App\Shared\Interface\Http\Router;

require dirname(__DIR__) . '/vendor/autoload.php';

$container = new Container();
$router = new Router();

$router->post(
    '/users',
    static fn (Request $request) =>
        $container->createUserController()->__invoke($request->body())
);

$router->get(
    '/users',
    static fn (Request $request) =>
        $container->listUsersController()->__invoke()
);

$router->get(
    '/users/{id}',
    static fn (Request $request) =>
        $container->getUserByIdController()->__invoke(
            (string) $request->param('id')
        )
);

$router->put(
    '/users/{id}',
    static fn (Request $request) =>
        $container->updateUserController()->__invoke(
            (string) $request->param('id'),
            $request->body()
        )
);

$router->delete(
    '/users/{id}',
    static fn (Request $request) =>
        $container->deleteUserController()->__invoke(
            (string) $request->param('id')
        )
);

$rawBody = file_get_contents('php://input');

$body = [];

if (is_string($rawBody) && $rawBody !== '') {
    /** @var array<string, mixed> $body */
    $body = json_decode($rawBody, true, flags: JSON_THROW_ON_ERROR);
}

$response = $router->dispatch(
    new Request(
        method: $_SERVER['REQUEST_METHOD'] ?? 'GET',
        path: parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/',
        body: $body,
    )
);

http_response_code($response->statusCode());

foreach ($response->headers() as $name => $value) {
    header($name . ': ' . $value);
}

echo $response->content();