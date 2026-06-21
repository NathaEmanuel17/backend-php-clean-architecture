<?php

declare(strict_types=1);

namespace App\User\Application\UseCase;

use App\User\Application\DTO\UserOutput;
use App\User\Domain\Exception\UserNotFound;
use App\User\Domain\Repository\UserRepository;
use App\User\Domain\ValueObject\UserId;

final readonly class GetUserByIdUseCase
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function execute(string $id): UserOutput
    {
        $user = $this->userRepository->findById(
            UserId::fromString($id)
        );

        if ($user === null) {
            throw new UserNotFound();
        }

        return new UserOutput(
            id: $user->id()->value(),
            name: $user->name()->value(),
            email: $user->email()->value(),
        );
    }
}
