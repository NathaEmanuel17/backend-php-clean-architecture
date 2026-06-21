<?php

declare(strict_types=1);

namespace Tests\Unit\User\Application\UseCase;

use App\Shared\Domain\Service\UuidGenerator;
use App\User\Application\Command\CreateUserCommand;
use App\User\Application\DTO\CreateUserOutput;
use App\User\Application\UseCase\CreateUserUseCase;
use App\User\Domain\Entity\User;
use App\User\Domain\Exception\EmailAlreadyExists;
use App\User\Domain\Repository\UserRepository;
use App\User\Domain\Service\PasswordHasher;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\PasswordHash;
use App\User\Domain\ValueObject\UserId;
use PHPUnit\Framework\TestCase;

final class CreateUserUseCaseTest extends TestCase
{
    public function testShouldCreateUser(): void
    {
        $repository = new class () implements UserRepository {
            public ?User $savedUser = null;

            public function save(User $user): void
            {
                $this->savedUser = $user;
            }

            public function findById(UserId $id): ?User
            {
                return null;
            }

            public function findByEmail(Email $email): ?User
            {
                return null;
            }

            /**
             * @return list<User>
             */
            public function findAll(): array
            {
                return [];
            }
        };

        $hasher = new class () implements PasswordHasher {
            public function hash(string $plainPassword): PasswordHash
            {
                return PasswordHash::fromString(
                    password_hash($plainPassword, PASSWORD_ARGON2ID)
                );
            }
        };

        $uuidGenerator = new class () implements UuidGenerator {
            public function generate(): string
            {
                return '550e8400-e29b-41d4-a716-446655440001';
            }
        };

        $useCase = new CreateUserUseCase($repository, $hasher, $uuidGenerator);

        $output = $useCase->execute(
            new CreateUserCommand(
                name: 'John Doe',
                email: 'john.doe@example.com',
                plainPassword: 'StrongPassword123!',
            )
        );

        self::assertInstanceOf(CreateUserOutput::class, $output);
        self::assertNotNull($repository->savedUser);
        self::assertSame('John Doe', $output->name);
        self::assertSame('john.doe@example.com', $output->email);
        self::assertSame('550e8400-e29b-41d4-a716-446655440001', $output->id);
    }

    public function testShouldThrowExceptionWhenEmailAlreadyExists(): void
    {
        $existingUser = User::create(
            UserId::fromString('550e8400-e29b-41d4-a716-446655440000'),
            \App\User\Domain\ValueObject\UserName::fromString('John Doe'),
            Email::fromString('john.doe@example.com'),
            PasswordHash::fromString(password_hash('StrongPassword123!', PASSWORD_ARGON2ID)),
        );

        $repository = new class ($existingUser) implements UserRepository {
            public function __construct(
                private readonly User $existingUser
            ) {
            }

            public function save(User $user): void
            {
            }

            public function findById(UserId $id): ?User
            {
                return null;
            }

            public function findByEmail(Email $email): User
            {
                return $this->existingUser;
            }

            /**
             * @return list<User>
             */
            public function findAll(): array
            {
                return [];
            }
        };

        $hasher = new class () implements PasswordHasher {
            public function hash(string $plainPassword): PasswordHash
            {
                return PasswordHash::fromString(
                    password_hash($plainPassword, PASSWORD_ARGON2ID)
                );
            }
        };

        $uuidGenerator = new class () implements UuidGenerator {
            public function generate(): string
            {
                return '550e8400-e29b-41d4-a716-446655440001';
            }
        };

        $useCase = new CreateUserUseCase($repository, $hasher, $uuidGenerator);

        $this->expectException(EmailAlreadyExists::class);

        $useCase->execute(
            new CreateUserCommand(
                name: 'Jane Doe',
                email: 'john.doe@example.com',
                plainPassword: 'StrongPassword123!',
            )
        );
    }
}
