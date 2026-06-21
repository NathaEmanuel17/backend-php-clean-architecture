<?php

declare(strict_types=1);

namespace Tests\Unit\User\Interface\Controller;

use App\Shared\Domain\Service\UuidGenerator;
use App\Shared\Interface\Exception\ExceptionResponseFactory;
use App\Shared\Interface\Response\JsonResponse;
use App\Shared\Interface\Response\ProblemJsonResponse;
use App\User\Application\UseCase\CreateUserUseCase;
use App\User\Domain\Entity\User;
use App\User\Domain\Repository\UserRepository;
use App\User\Domain\Service\PasswordHasher;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\PasswordHash;
use App\User\Domain\ValueObject\UserId;
use App\User\Domain\ValueObject\UserName;
use App\User\Interface\Controller\CreateUserController;
use PHPUnit\Framework\TestCase;

final class CreateUserControllerTest extends TestCase
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
                return '550e8400-e29b-41d4-a716-446655440000';
            }
        };

        $controller = new CreateUserController(
            new CreateUserUseCase(
                $repository,
                $hasher,
                $uuidGenerator,
            ),
            new ExceptionResponseFactory(),
        );

        $response = $controller->__invoke([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'StrongPassword123!',
        ]);

        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertSame(201, $response->statusCode());
        self::assertSame(
            '{"id":"550e8400-e29b-41d4-a716-446655440000","name":"John Doe","email":"john.doe@example.com"}',
            $response->content()
        );
    }

    public function testShouldReturnProblemResponseWhenEmailAlreadyExists(): void
    {
        $repository = new class () implements UserRepository {
            public function save(User $user): void
            {
            }

            public function findById(UserId $id): ?User
            {
                return null;
            }

            public function findByEmail(
                Email $email
            ): ?User {
                if (
                    $email->value() !==
                    'john.doe@example.com'
                ) {
                    return null;
                }

                return User::create(
                    UserId::fromString(
                        '550e8400-e29b-41d4-a716-446655440000'
                    ),
                    UserName::fromString(
                        'Existing User'
                    ),
                    $email,
                    PasswordHash::fromString(
                        password_hash(
                            'Password123!',
                            PASSWORD_ARGON2ID
                        )
                    ),
                );
            }

            public function findAll(): array
            {
                return [];
            }
        };

        $hasher = new class () implements PasswordHasher {
            public function hash(
                string $plainPassword
            ): PasswordHash {
                return PasswordHash::fromString(
                    password_hash(
                        $plainPassword,
                        PASSWORD_ARGON2ID
                    )
                );
            }
        };

        $uuidGenerator = new class () implements UuidGenerator {
            public function generate(): string
            {
                return '550e8400-e29b-41d4-a716-446655440000';
            }
        };

        $controller = new CreateUserController(
            new CreateUserUseCase(
                $repository,
                $hasher,
                $uuidGenerator,
            ),
            new ExceptionResponseFactory(),
        );

        $response = $controller->__invoke([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'StrongPassword123!',
        ]);

        self::assertInstanceOf(
            ProblemJsonResponse::class,
            $response
        );

        self::assertSame(
            409,
            $response->statusCode()
        );
    }
}
