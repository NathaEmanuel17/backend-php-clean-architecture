<?php

declare(strict_types=1);

namespace App\User\Interface\Controller;

use App\Shared\Interface\Exception\ExceptionResponseFactory;
use App\Shared\Interface\Response\JsonResponse;
use App\Shared\Interface\Response\ProblemJsonResponse;
use App\User\Application\UseCase\DeleteUserUseCase;
use Throwable;

final readonly class DeleteUserController
{
    public function __construct(
        private DeleteUserUseCase $deleteUserUseCase,
        private ExceptionResponseFactory $exceptionResponseFactory,
    ) {
    }

    public function __invoke(string $id): JsonResponse|ProblemJsonResponse
    {
        try {
            $this->deleteUserUseCase->execute($id);

            return new JsonResponse(
                data: [],
                statusCode: 204,
            );
        } catch (Throwable $exception) {
            return $this
                ->exceptionResponseFactory
                ->fromException($exception);
        }
    }
}
