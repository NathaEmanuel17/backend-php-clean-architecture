<?php

declare(strict_types=1);

namespace App\User\Interface\Controller;

use App\Shared\Interface\Response\JsonResponse;
use App\Shared\Interface\Response\ProblemJsonResponse;
use App\User\Application\Command\CreateUserCommand;
use App\User\Application\UseCase\CreateUserUseCase;
use App\User\Domain\Exception\EmailAlreadyExists;
use App\User\Interface\Request\CreateUserRequest;

final readonly class CreateUserController
{
    public function __construct(
        private CreateUserUseCase $createUserUseCase,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function __invoke(array $payload): JsonResponse|ProblemJsonResponse
    {
        $request = CreateUserRequest::fromArray($payload);

        try {
            $output = $this->createUserUseCase->execute(
                new CreateUserCommand(
                    name: $request->name,
                    email: $request->email,
                    plainPassword: $request->password,
                )
            );
        } catch (EmailAlreadyExists $exception) {
            return new ProblemJsonResponse(
                type: 'https://api.example.com/problems/email-already-exists',
                title: 'Email already exists',
                statusCode: 409,
                detail: $exception->getMessage(),
            );
        }

        return new JsonResponse(
            data: [
                'id' => $output->id,
                'name' => $output->name,
                'email' => $output->email,
            ],
            statusCode: 201,
        );
    }
}
