<?php

declare(strict_types=1);

namespace App\User\Interface\Controller;

use App\Shared\Interface\Exception\ExceptionResponseFactory;
use App\Shared\Interface\Response\JsonResponse;
use App\Shared\Interface\Response\ProblemJsonResponse;
use App\User\Application\UseCase\ListUsersUseCase;
use Throwable;

final readonly class ListUsersController
{
    public function __construct(
        private ListUsersUseCase $listUsersUseCase,
        private ExceptionResponseFactory $exceptionResponseFactory,
    ) {
    }

    public function __invoke(): JsonResponse|ProblemJsonResponse
    {
        try {
            $users = $this->listUsersUseCase->execute();

            return new JsonResponse(
                data: [
                    'data' => array_map(
                        static fn ($user): array => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                        ],
                        $users
                    ),
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
