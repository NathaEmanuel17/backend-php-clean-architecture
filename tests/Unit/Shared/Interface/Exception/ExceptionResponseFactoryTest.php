<?php

declare(strict_types=1);

namespace Tests\Unit\Shared\Interface\Exception;

use App\Shared\Interface\Exception\ExceptionResponseFactory;
use App\Shared\Interface\Response\ProblemJsonResponse;
use App\User\Domain\Exception\EmailAlreadyExists;
use App\User\Domain\Exception\UserNotFound;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ExceptionResponseFactoryTest extends TestCase
{
    public function testShouldMapEmailAlreadyExists(): void
    {
        $factory = new ExceptionResponseFactory();

        $response = $factory->fromException(
            new EmailAlreadyExists()
        );

        self::assertInstanceOf(
            ProblemJsonResponse::class,
            $response
        );

        self::assertSame(
            409,
            $response->statusCode()
        );
    }

    public function testShouldMapUserNotFound(): void
    {
        $factory = new ExceptionResponseFactory();

        $response = $factory->fromException(
            new UserNotFound()
        );

        self::assertSame(
            404,
            $response->statusCode()
        );
    }

    public function testShouldMapInvalidArgumentException(): void
    {
        $factory = new ExceptionResponseFactory();

        $response = $factory->fromException(
            new InvalidArgumentException(
                'Invalid payload.'
            )
        );

        self::assertSame(
            400,
            $response->statusCode()
        );
    }

    public function testShouldMapUnknownException(): void
    {
        $factory = new ExceptionResponseFactory();

        $response = $factory->fromException(
            new RuntimeException(
                'Unexpected error.'
            )
        );

        self::assertSame(
            500,
            $response->statusCode()
        );
    }
}
