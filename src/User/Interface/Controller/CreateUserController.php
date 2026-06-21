<?php

declare(strict_types=1);

namespace App\User\Interface\Controller;

use App\Shared\Interface\Exception\ExceptionResponseFactory;
use App\Shared\Interface\Response\JsonResponse;
use App\Shared\Interface\Response\ProblemJsonResponse;
use App\User\Application\Command\CreateUserCommand;
use App\User\Application\UseCase\CreateUserUseCase;
use App\User\Interface\Request\CreateUserRequest;
use Throwable;

final readonly class CreateUserController
{
    public function __construct(
        private CreateUserUseCase $createUserUseCase,
        private ExceptionResponseFactory $exceptionResponseFactory,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function __invoke(
        array $payload
    ): JsonResponse|ProblemJsonResponse {
        try {
            $request = CreateUserRequest::fromArray(
                $payload
            );

            $output = $this->createUserUseCase->execute(
                new CreateUserCommand(
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
                statusCode: 201,
            );
        } catch (Throwable $exception) {
            return $this
                ->exceptionResponseFactory
                ->fromException($exception);
        }
    }
}
