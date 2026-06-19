<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Mapper;

use App\User\Domain\Entity\User;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\PasswordHash;
use App\User\Domain\ValueObject\UserId;
use App\User\Domain\ValueObject\UserName;

final readonly class UserMapper
{
    /**
     * @param array{id: string, name: string, email: string, password_hash: string} $row
     */
    public function toEntity(array $row): User
    {
        return User::create(
            UserId::fromString($row['id']),
            UserName::fromString($row['name']),
            Email::fromString($row['email']),
            PasswordHash::fromString($row['password_hash']),
        );
    }
}
