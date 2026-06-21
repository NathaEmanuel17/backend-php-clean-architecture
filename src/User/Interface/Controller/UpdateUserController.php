<?php

declare(strict_types=1);

namespace App\User\Interface\Controller;

use App\Shared\Interface\Exception\ExceptionResponseFactory;
use App\Shared\Interface\Response\JsonResponse;
use App\Shared\Interface\Response\ProblemJsonResponse;
use App\User\Application\Command\UpdateUserCommand;
use App\User\Application\UseCase\UpdateUserUseCase;
use App\User\Interface\Request\CreateUserRequest;
use Throwable;

final readonly class UpdateUserController
{
    public function __construct(
        private UpdateUserUseCase $updateUserUseCase,
        private ExceptionResponseFactory $exceptionResponseFactory,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function __invoke(
        string $id,
        array $payload
    ): JsonResponse|ProblemJsonResponse {
        try {
            $request = CreateUserRequest::fromArray($payload);

            $output = $this->updateUserUseCase->execute(
                new UpdateUserCommand(
                    id: $id,
                    name: $request->name,
                    email: $request->email,
                    plainPassword: $request->password,
                )
            );

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
