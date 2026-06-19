<?php

declare(strict_types=1);

namespace Tests\Integration\User\Infrastructure\Repository;

use App\Shared\Infrastructure\Persistence\PdoConnectionFactory;
use App\User\Domain\Entity\User;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\PasswordHash;
use App\User\Domain\ValueObject\UserId;
use App\User\Domain\ValueObject\UserName;
use App\User\Infrastructure\Repository\PostgresUserRepository;
use PDO;
use PHPUnit\Framework\TestCase;

final class PostgresUserRepositoryTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = PdoConnectionFactory::create();

        $this->pdo->exec('DELETE FROM users');
    }

    public function testShouldSaveUser(): void
    {
        $repository = new PostgresUserRepository(
            $this->pdo
        );

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

        $repository->save($user);

        $statement = $this->pdo->prepare(
            'SELECT COUNT(*) FROM users WHERE id = :id'
        );

        $statement->execute([
            'id' => $user->id()->value(),
        ]);

        $count = $statement->fetchColumn();

        self::assertSame(
            1,
            (int) $count
        );
    }

    public function testShouldFindUserById(): void
    {
        $repository = new PostgresUserRepository($this->pdo);

        $user = User::create(
            UserId::fromString('550e8400-e29b-41d4-a716-446655440000'),
            UserName::fromString('John Doe'),
            Email::fromString('john.doe@example.com'),
            PasswordHash::fromString(
                password_hash('StrongPassword123!', PASSWORD_ARGON2ID)
            ),
        );

        $repository->save($user);

        $foundUser = $repository->findById($user->id());

        self::assertNotNull($foundUser);
        self::assertSame($user->id()->value(), $foundUser->id()->value());
        self::assertSame($user->name()->value(), $foundUser->name()->value());
        self::assertSame($user->email()->value(), $foundUser->email()->value());
    }

    public function testShouldFindUserByEmail(): void
    {
        $repository = new PostgresUserRepository($this->pdo);

        $user = User::create(
            UserId::fromString('550e8400-e29b-41d4-a716-446655440000'),
            UserName::fromString('John Doe'),
            Email::fromString('john.doe@example.com'),
            PasswordHash::fromString(
                password_hash('StrongPassword123!', PASSWORD_ARGON2ID)
            ),
        );

        $repository->save($user);

        $foundUser = $repository->findByEmail($user->email());

        self::assertNotNull($foundUser);
        self::assertSame($user->id()->value(), $foundUser->id()->value());
        self::assertSame($user->name()->value(), $foundUser->name()->value());
        self::assertSame($user->email()->value(), $foundUser->email()->value());
    }

    public function testShouldUpdateUserWhenSavingExistingId(): void
    {
        $repository = new PostgresUserRepository($this->pdo);

        $user = User::create(
            UserId::fromString('550e8400-e29b-41d4-a716-446655440000'),
            UserName::fromString('John Doe'),
            Email::fromString('john.doe@example.com'),
            PasswordHash::fromString(
                password_hash('StrongPassword123!', PASSWORD_ARGON2ID)
            ),
        );

        $repository->save($user);

        $user->changeName(UserName::fromString('Jane Doe'));

        $repository->save($user);

        $foundUser = $repository->findById($user->id());

        self::assertNotNull($foundUser);
        self::assertSame('Jane Doe', $foundUser->name()->value());
    }
}
