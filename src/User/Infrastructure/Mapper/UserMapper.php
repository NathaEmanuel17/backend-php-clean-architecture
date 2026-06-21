<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Mapper;

use App\User\Domain\Entity\User;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\PasswordHash;
use App\User\Domain\ValueObject\UserId;
use App\User\Domain\ValueObject\UserName;
use DateTimeImmutable;

final readonly class UserMapper
{
    /**
     * @param array{
     *     id: string,
     *     name: string,
     *     email: string,
     *     password_hash: string,
     *     created_at: string,
     *     updated_at: string,
     *     deleted_at?: string|null
     * } $row
     */
    public function toEntity(array $row): User
    {
        $deletedAt = $row['deleted_at'] ?? null;

        return User::reconstitute(
            UserId::fromString($row['id']),
            UserName::fromString($row['name']),
            Email::fromString($row['email']),
            PasswordHash::fromString($row['password_hash']),
            new DateTimeImmutable($row['created_at']),
            new DateTimeImmutable($row['updated_at']),
            $deletedAt === null ? null : new DateTimeImmutable($deletedAt),
        );
    }
}
