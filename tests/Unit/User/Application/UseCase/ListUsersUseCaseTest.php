<?php

declare(strict_types=1);

namespace Tests\Unit\User\Application\UseCase;

use App\User\Application\DTO\UserOutput;
use App\User\Application\UseCase\ListUsersUseCase;
use App\User\Domain\Entity\User;
use App\User\Domain\Repository\UserRepository;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\PasswordHash;
use App\User\Domain\ValueObject\UserId;
use App\User\Domain\ValueObject\UserName;
use PHPUnit\Framework\TestCase;

final class ListUsersUseCaseTest extends TestCase
{
    public function testShouldListUsers(): void
    {
        $user = User::create(
            UserId::fromString(
                '550e8400-e29b-41d4-a716-446655440000'
            ),
            UserName::fromString(
                'John Doe'
            ),
            Email::fromString(
                'john.doe@example.com'
            ),
            PasswordHash::fromString(
                password_hash(
                    'StrongPassword123!',
                    PASSWORD_ARGON2ID
                )
            ),
        );

        $repository = new class ($user) implements UserRepository {
            public function __construct(
                private readonly User $user
            ) {
            }

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
                return [$this->user];
            }
        };

        $useCase = new ListUsersUseCase(
            $repository
        );

        $users = $useCase->execute();

        self::assertCount(1, $users);

        self::assertInstanceOf(
            UserOutput::class,
            $users[0]
        );
    }
}
