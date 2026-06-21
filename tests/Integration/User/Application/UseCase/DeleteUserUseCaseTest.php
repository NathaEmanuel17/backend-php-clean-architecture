<?php

declare(strict_types=1);

namespace Tests\Integration\User\Application\UseCase;

use App\Shared\Infrastructure\Persistence\PdoConnectionFactory;
use App\User\Application\UseCase\DeleteUserUseCase;
use App\User\Domain\Entity\User;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\PasswordHash;
use App\User\Domain\ValueObject\UserId;
use App\User\Domain\ValueObject\UserName;
use App\User\Infrastructure\Repository\PostgresUserRepository;
use PDO;
use PHPUnit\Framework\TestCase;

final class DeleteUserUseCaseTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = PdoConnectionFactory::create();
        $this->pdo->exec('DELETE FROM users');
    }

    public function testShouldSoftDeleteUserInDatabase(): void
    {
        $repository = new PostgresUserRepository($this->pdo);

        $user = User::create(
            UserId::fromString('550e8400-e29b-41d4-a716-446655440000'),
            UserName::fromString('John Doe'),
            Email::fromString('john.doe@example.com'),
            PasswordHash::fromString(
                password_hash('Password123!', PASSWORD_ARGON2ID)
            ),
        );

        $repository->save($user);

        $useCase = new DeleteUserUseCase($repository);

        $useCase->execute('550e8400-e29b-41d4-a716-446655440000');

        $foundUser = $repository->findById($user->id());

        self::assertNull($foundUser);

        $statement = $this->pdo->prepare(
            'SELECT deleted_at FROM users WHERE id = :id'
        );

        $statement->execute([
            'id' => $user->id()->value(),
        ]);

        $deletedAt = $statement->fetchColumn();

        self::assertIsString($deletedAt);
    }
}
