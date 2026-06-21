<?php

declare(strict_types=1);

namespace App\Shared\Interface\Exception;

use App\Shared\Interface\Response\ProblemJsonResponse;
use App\User\Domain\Exception\EmailAlreadyExists;
use App\User\Domain\Exception\UserNotFound;
use InvalidArgumentException;
use Throwable;

final readonly class ExceptionResponseFactory
{
    public function fromException(
        Throwable $exception
    ): ProblemJsonResponse {
        return match (true) {
            $exception instanceof EmailAlreadyExists =>
                new ProblemJsonResponse(
                    type: 'https://api.example.com/problems/email-already-exists',
                    title: 'Email already exists',
                    statusCode: 409,
                    detail: $exception->getMessage(),
                ),

            $exception instanceof UserNotFound =>
                new ProblemJsonResponse(
                    type: 'https://api.example.com/problems/user-not-found',
                    title: 'User not found',
                    statusCode: 404,
                    detail: $exception->getMessage(),
                ),

            $exception instanceof InvalidArgumentException =>
                new ProblemJsonResponse(
                    type: 'https://api.example.com/problems/invalid-request',
                    title: 'Invalid request',
                    statusCode: 400,
                    detail: $exception->getMessage(),
                ),

            default =>
                new ProblemJsonResponse(
                    type: 'https://api.example.com/problems/internal-server-error',
                    title: 'Internal server error',
                    statusCode: 500,
                    detail: 'An unexpected error occurred.',
                ),
        };
    }
}
