<?php

declare(strict_types=1);

namespace App\User\Application\UseCase;

use App\Shared\Domain\Service\UuidGenerator;
use App\User\Application\Command\CreateUserCommand;
use App\User\Application\DTO\CreateUserOutput;
use App\User\Domain\Entity\User;
use App\User\Domain\Exception\EmailAlreadyExists;
use App\User\Domain\Repository\UserRepository;
use App\User\Domain\Service\PasswordHasher;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\UserId;
use App\User\Domain\ValueObject\UserName;

final readonly class CreateUserUseCase
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordHasher $passwordHasher,
        private UuidGenerator $uuidGenerator,
    ) {
    }

    public function execute(CreateUserCommand $command): CreateUserOutput
    {
        $email = Email::fromString($command->email);

        if ($this->userRepository->findByEmail($email) !== null) {
            throw new EmailAlreadyExists();
        }

        $user = User::create(
            id: UserId::fromString($this->uuidGenerator->generate()),
            name: UserName::fromString($command->name),
            email: $email,
            passwordHash: $this->passwordHasher->hash($command->plainPassword),
        );

        $this->userRepository->save($user);

        return new CreateUserOutput(
            id: $user->id()->value(),
            name: $user->name()->value(),
            email: $user->email()->value(),
        );
    }
}
