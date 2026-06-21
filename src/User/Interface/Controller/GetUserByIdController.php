<?php

declare(strict_types=1);

namespace App\User\Interface\Controller;

use App\Shared\Interface\Exception\ExceptionResponseFactory;
use App\Shared\Interface\Response\JsonResponse;
use App\Shared\Interface\Response\ProblemJsonResponse;
use App\User\Application\UseCase\GetUserByIdUseCase;
use Throwable;

final readonly class GetUserByIdController
{
    public function __construct(
        private GetUserByIdUseCase $getUserByIdUseCase,
        private ExceptionResponseFactory $exceptionResponseFactory,
    ) {
    }

    public function __invoke(string $id): JsonResponse|ProblemJsonResponse
    {
        try {
            $output = $this->getUserByIdUseCase->execute($id);

            return new JsonResponse(
                data: [
                    'id' => $output->id,
                    'name' => $output->name,
                    'email' => $output->email,
                ],
                statusCode: 200,
            );
        } catch (Throwable $exception) {
            return $this
                ->exceptionResponseFactory
                ->fromException($exception);
        }
    }
}
