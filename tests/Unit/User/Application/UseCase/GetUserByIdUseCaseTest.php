<?php

declare(strict_types=1);

namespace Tests\Unit\User\Application\UseCase;

use App\User\Application\DTO\UserOutput;
use App\User\Application\UseCase\GetUserByIdUseCase;
use App\User\Domain\Entity\User;
use App\User\Domain\Exception\UserNotFound;
use App\User\Domain\Repository\UserRepository;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\PasswordHash;
use App\User\Domain\ValueObject\UserId;
use App\User\Domain\ValueObject\UserName;
use PHPUnit\Framework\TestCase;

final class GetUserByIdUseCaseTest extends TestCase
{
    public function testShouldGetUserById(): void
    {
        $user = User::create(
            UserId::fromString('550e8400-e29b-41d4-a716-446655440000'),
            UserName::fromString('John Doe'),
            Email::fromString('john.doe@example.com'),
            PasswordHash::fromString(password_hash('StrongPassword123!', PASSWORD_ARGON2ID)),
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
                return $this->user;
            }

            public function findByEmail(Email $email): ?User
            {
                return null;
            }
        };

        $useCase = new GetUserByIdUseCase($repository);

        $output = $useCase->execute('550e8400-e29b-41d4-a716-446655440000');

        self::assertInstanceOf(UserOutput::class, $output);
        self::assertSame('550e8400-e29b-41d4-a716-446655440000', $output->id);
        self::assertSame('John Doe', $output->name);
        self::assertSame('john.doe@example.com', $output->email);
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
        };

        $useCase = new GetUserByIdUseCase($repository);

        $this->expectException(UserNotFound::class);

        $useCase->execute('550e8400-e29b-41d4-a716-446655440000');
    }
}
