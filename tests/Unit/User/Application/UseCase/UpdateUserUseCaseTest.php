<?php

declare(strict_types=1);

namespace Tests\Unit\User\Application\UseCase;

use App\User\Application\Command\UpdateUserCommand;
use App\User\Application\DTO\UserOutput;
use App\User\Application\UseCase\UpdateUserUseCase;
use App\User\Domain\Entity\User;
use App\User\Domain\Exception\UserNotFound;
use App\User\Domain\Repository\UserRepository;
use App\User\Domain\Service\PasswordHasher;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\PasswordHash;
use App\User\Domain\ValueObject\UserId;
use App\User\Domain\ValueObject\UserName;
use PHPUnit\Framework\TestCase;

final class UpdateUserUseCaseTest extends TestCase
{
    public function testShouldUpdateUser(): void
    {
        $user = User::create(
            UserId::fromString('550e8400-e29b-41d4-a716-446655440000'),
            UserName::fromString('John Doe'),
            Email::fromString('john.doe@example.com'),
            PasswordHash::fromString(password_hash('StrongPassword123!', PASSWORD_ARGON2ID)),
        );

        $repository = new class ($user) implements UserRepository {
            public function __construct(private User $user)
            {
            }

            public function save(User $user): void
            {
                $this->user = $user;
            }

            public function findById(UserId $id): ?User
            {
                return $id->value() === $this->user->id()->value()
                    ? $this->user
                    : null;
            }

            public function findByEmail(Email $email): ?User
            {
                return null;
            }

            public function findAll(): array
            {
                return [$this->user];
            }
        };

        $hasher = new class () implements PasswordHasher {
            public function hash(string $plainPassword): PasswordHash
            {
                return PasswordHash::fromString(password_hash($plainPassword, PASSWORD_ARGON2ID));
            }
        };

        $useCase = new UpdateUserUseCase($repository, $hasher);

        $output = $useCase->execute(
            new UpdateUserCommand(
                id: '550e8400-e29b-41d4-a716-446655440000',
                name: 'Jane Doe',
                email: 'jane.doe@example.com',
                plainPassword: 'NewStrongPassword123!',
            )
        );

        self::assertInstanceOf(UserOutput::class, $output);
        self::assertSame('Jane Doe', $output->name);
        self::assertSame('jane.doe@example.com', $output->email);
    }

    public function testShouldThrowExceptionWhenUserDoesNotExist(): void
    {
        $repository = new class () implements UserRepository {
            public function save(User $user): void
            {
            }

            public function findById(UserId $id): ?User
            {
                return null;
            }

            public function findByEmail(Email $email): ?User
            {
                return null;
            }

            public function findAll(): array
            {
                return [];
            }
        };

        $hasher = new class () implements PasswordHasher {
            public function hash(string $plainPassword): PasswordHash
            {
                return PasswordHash::fromString(password_hash($plainPassword, PASSWORD_ARGON2ID));
            }
        };

        $useCase = new UpdateUserUseCase($repository, $hasher);

        $this->expectException(UserNotFound::class);

        $useCase->execute(
            new UpdateUserCommand(
                id: '550e8400-e29b-41d4-a716-446655440000',
                name: 'Jane Doe',
                email: 'jane.doe@example.com',
                plainPassword: 'NewStrongPassword123!',
            )
        );
    }
}
