<?php

declare(strict_types=1);

namespace App\User\Application\UseCase;

use App\User\Application\DTO\UserOutput;
use App\User\Domain\Repository\UserRepository;

final readonly class ListUsersUseCase
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    /**
     * @return list<UserOutput>
     */
    public function execute(): array
    {
        $users = $this->userRepository->findAll();

        return array_map(
            static fn ($user): UserOutput => new UserOutput(
                id: $user->id()->value(),
                name: $user->name()->value(),
                email: $user->email()->value(),
            ),
            $users
        );
    }
}
