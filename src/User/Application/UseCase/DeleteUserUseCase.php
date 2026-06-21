<?php

declare(strict_types=1);

namespace App\User\Application\UseCase;

use App\User\Domain\Exception\UserNotFound;
use App\User\Domain\Repository\UserRepository;
use App\User\Domain\ValueObject\UserId;

final readonly class DeleteUserUseCase
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function execute(string $id): void
    {
        $user = $this->userRepository->findById(
            UserId::fromString($id)
        );

        if ($user === null) {
            throw new UserNotFound();
        }

        $user->delete();

        $this->userRepository->save($user);
    }
}
