<?php

declare(strict_types=1);

namespace Tests\Integration\User\Application\UseCase;

use App\Shared\Domain\Service\UuidGenerator;
use App\Shared\Infrastructure\Persistence\PdoConnectionFactory;
use App\User\Application\Command\CreateUserCommand;
use App\User\Application\UseCase\CreateUserUseCase;
use App\User\Domain\Exception\EmailAlreadyExists;
use App\User\Infrastructure\Repository\PostgresUserRepository;
use App\User\Infrastructure\Service\Argon2idPasswordHasher;
use PDO;
use PHPUnit\Framework\TestCase;

final class CreateUserUseCaseTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = PdoConnectionFactory::create();

        $this->pdo->exec('DELETE FROM users');
    }

    public function testShouldCreateUserUsingPostgresRepository(): void
    {
        $useCase = new CreateUserUseCase(
            new PostgresUserRepository($this->pdo),
            new Argon2idPasswordHasher(),
            new class () implements UuidGenerator {
                public function generate(): string
                {
                    return '550e8400-e29b-41d4-a716-446655440000';
                }
            },
        );

        $output = $useCase->execute(
            new CreateUserCommand(
                name: 'John Doe',
                email: 'john.doe@example.com',
                plainPassword: 'StrongPassword123!',
            )
        );

        self::assertSame('550e8400-e29b-41d4-a716-446655440000', $output->id);
        self::assertSame('John Doe', $output->name);
        self::assertSame('john.doe@example.com', $output->email);

        $statement = $this->pdo->prepare(
            'SELECT email FROM users WHERE id = :id'
        );

        $statement->execute([
            'id' => $output->id,
        ]);

        $email = $statement->fetchColumn();

        self::assertIsString($email);
        self::assertSame('john.doe@example.com', $email);
    }

    public function testShouldPreventDuplicatedEmailUsingPostgresRepository(): void
    {
        $useCase = new CreateUserUseCase(
            new PostgresUserRepository($this->pdo),
            new Argon2idPasswordHasher(),
            new class () implements UuidGenerator {
                public function generate(): string
                {
                    return '550e8400-e29b-41d4-a716-446655440000';
                }
            },
        );

        $command = new CreateUserCommand(
            name: 'John Doe',
            email: 'john.doe@example.com',
            plainPassword: 'StrongPassword123!',
        );

        $useCase->execute($command);

        $this->expectException(EmailAlreadyExists::class);

        $useCase->execute(
            new CreateUserCommand(
                name: 'Jane Doe',
                email: 'john.doe@example.com',
                plainPassword: 'AnotherStrongPassword123!',
            )
        );
    }
}
