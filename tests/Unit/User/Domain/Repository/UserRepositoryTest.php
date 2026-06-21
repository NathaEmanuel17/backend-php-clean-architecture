<?php

declare(strict_types=1);

namespace Tests\Unit\User\Domain\Repository;

use App\User\Domain\Entity\User;
use App\User\Domain\Repository\UserRepository;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\PasswordHash;
use App\User\Domain\ValueObject\UserId;
use App\User\Domain\ValueObject\UserName;
use PHPUnit\Framework\TestCase;

final class UserRepositoryTest extends TestCase
{
    public function testShouldSaveAndFindUserById(): void
    {
        $repository = new class () implements UserRepository {
            /** @var array<string, User> */
            private array $users = [];

            public function save(User $user): void
            {
                $this->users[$user->id()->value()] = $user;
            }

            public function findById(UserId $id): ?User
            {
                return $this->users[$id->value()] ?? null;
            }

            public function findByEmail(Email $email): ?User
            {
                foreach ($this->users as $user) {
                    if ($user->email()->value() === $email->value()) {
                        return $user;
                    }
                }

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

        $user = User::create(
            UserId::fromString('550e8400-e29b-41d4-a716-446655440000'),
            UserName::fromString('John Doe'),
            Email::fromString('john.doe@example.com'),
            PasswordHash::fromString(password_hash('StrongPassword123!', PASSWORD_ARGON2ID)),
        );

        $repository->save($user);

        self::assertSame($user, $repository->findById($user->id()));
    }

    public function testShouldSaveAndFindUserByEmail(): void
    {
        $repository = new class () implements UserRepository {
            /** @var array<string, User> */
            private array $users = [];

            public function save(User $user): void
            {
                $this->users[$user->id()->value()] = $user;
            }

            public function findById(UserId $id): ?User
            {
                return $this->users[$id->value()] ?? null;
            }

            public function findByEmail(Email $email): ?User
            {
                foreach ($this->users as $user) {
                    if ($user->email()->value() === $email->value()) {
                        return $user;
                    }
                }

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

        $user = User::create(
            UserId::fromString('550e8400-e29b-41d4-a716-446655440000'),
            UserName::fromString('John Doe'),
            Email::fromString('john.doe@example.com'),
            PasswordHash::fromString(password_hash('StrongPassword123!', PASSWORD_ARGON2ID)),
        );

        $repository->save($user);

        self::assertSame($user, $repository->findByEmail($user->email()));
    }
}
