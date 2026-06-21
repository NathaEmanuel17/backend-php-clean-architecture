<?php

declare(strict_types=1);

namespace App\User\Application\UseCase;

use App\User\Application\Command\UpdateUserCommand;
use App\User\Application\DTO\UserOutput;
use App\User\Domain\Exception\UserNotFound;
use App\User\Domain\Repository\UserRepository;
use App\User\Domain\Service\PasswordHasher;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\UserId;
use App\User\Domain\ValueObject\UserName;

final readonly class UpdateUserUseCase
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordHasher $passwordHasher,
    ) {
    }

    public function execute(UpdateUserCommand $command): UserOutput
    {
        $user = $this->userRepository->findById(
            UserId::fromString($command->id)
        );

        if ($user === null) {
            throw new UserNotFound();
        }

        $user->changeName(
            UserName::fromString($command->name)
        );

        $user->changeEmail(
            Email::fromString($command->email)
        );

        $user->changePasswordHash(
            $this->passwordHasher->hash($command->plainPassword)
        );

        $this->userRepository->save($user);

        return new UserOutput(
            id: $user->id()->value(),
            name: $user->name()->value(),
            email: $user->email()->value(),
        );
    }
}
