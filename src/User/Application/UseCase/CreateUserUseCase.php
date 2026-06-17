<?php

declare(strict_types=1);

namespace App\User\Application\UseCase;

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
    ) {
    }

    public function execute(CreateUserCommand $command): CreateUserOutput
    {
        $email = Email::fromString($command->email);

        if ($this->userRepository->findByEmail($email) !== null) {
            throw new EmailAlreadyExists();
        }

        $user = User::create(
            id: UserId::fromString(self::generateUuidV4()),
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

    private static function generateUuidV4(): string
    {
        $data = random_bytes(16);

        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        return vsprintf(
            '%s%s-%s-%s-%s-%s%s%s',
            str_split(bin2hex($data), 4)
        );
    }
}
