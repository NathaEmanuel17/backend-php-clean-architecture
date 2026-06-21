<?php

declare(strict_types=1);

namespace Tests\Unit\User\Interface\Controller;

use App\Shared\Interface\Exception\ExceptionResponseFactory;
use App\Shared\Interface\Response\JsonResponse;
use App\User\Application\UseCase\UpdateUserUseCase;
use App\User\Domain\Entity\User;
use App\User\Domain\Repository\UserRepository;
use App\User\Domain\Service\PasswordHasher;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\PasswordHash;
use App\User\Domain\ValueObject\UserId;
use App\User\Domain\ValueObject\UserName;
use App\User\Interface\Controller\UpdateUserController;
use PHPUnit\Framework\TestCase;

final class UpdateUserControllerTest extends TestCase
{
    public function testShouldUpdateUser(): void
    {
        $user = User::create(
            UserId::fromString('550e8400-e29b-41d4-a716-446655440000'),
            UserName::fromString('John Doe'),
            Email::fromString('john.doe@example.com'),
            PasswordHash::fromString(password_hash('Password123!', PASSWORD_ARGON2ID)),
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
                return PasswordHash::fromString(
                    password_hash($plainPassword, PASSWORD_ARGON2ID)
                );
            }
        };

        $controller = new UpdateUserController(
            new UpdateUserUseCase($repository, $hasher),
            new ExceptionResponseFactory(),
        );

        $response = $controller->__invoke(
            '550e8400-e29b-41d4-a716-446655440000',
            [
                'name' => 'Jane Doe',
                'email' => 'jane.doe@example.com',
                'password' => 'NewPassword123!',
            ]
        );

        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertSame(200, $response->statusCode());
        self::assertSame(
            '{"id":"550e8400-e29b-41d4-a716-446655440000","name":"Jane Doe","email":"jane.doe@example.com"}',
            $response->content()
        );
    }
}
