<?php

declare(strict_types=1);

namespace Tests\Unit\User\Domain\Entity;

use App\User\Domain\Entity\User;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\PasswordHash;
use App\User\Domain\ValueObject\UserId;
use App\User\Domain\ValueObject\UserName;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    public function testShouldCreateUser(): void
    {
        $id = UserId::fromString(
            '550e8400-e29b-41d4-a716-446655440000'
        );

        $name = UserName::fromString(
            'John Doe'
        );

        $email = Email::fromString(
            'john.doe@example.com'
        );

        $passwordHash = PasswordHash::fromString(
            password_hash(
                'StrongPassword123!',
                PASSWORD_ARGON2ID
            )
        );

        $user = User::create(
            $id,
            $name,
            $email,
            $passwordHash
        );

        self::assertSame($id, $user->id());
        self::assertSame($name, $user->name());
        self::assertSame($email, $user->email());
        self::assertSame(
            $passwordHash,
            $user->passwordHash()
        );
    }

    public function testShouldChangeUserName(): void
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

        $newName = UserName::fromString(
            'Jane Doe'
        );

        $user->changeName($newName);

        self::assertSame(
            $newName,
            $user->name()
        );
    }

    public function testShouldChangeUserEmail(): void
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

        $newEmail = Email::fromString(
            'jane.doe@example.com'
        );

        $user->changeEmail($newEmail);

        self::assertSame(
            $newEmail,
            $user->email()
        );
    }

    public function testShouldChangeUserPasswordHash(): void
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

        $newPasswordHash = PasswordHash::fromString(
            password_hash(
                'AnotherStrongPassword123!',
                PASSWORD_ARGON2ID
            )
        );

        $user->changePasswordHash(
            $newPasswordHash
        );

        self::assertSame(
            $newPasswordHash,
            $user->passwordHash()
        );
    }

    public function testShouldCreateUserWithCreatedAt(): void
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

        self::assertInstanceOf(
            DateTimeImmutable::class,
            $user->createdAt()
        );
    }
}
