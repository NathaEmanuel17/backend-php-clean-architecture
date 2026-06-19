<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Repository;

use App\User\Domain\Entity\User;
use App\User\Domain\Repository\UserRepository;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\UserId;
use PDO;

final readonly class PostgresUserRepository implements UserRepository
{
    public function __construct(
        private PDO $pdo,
    ) {
    }

    public function save(User $user): void
    {
        $sql = <<<SQL
            INSERT INTO users (
                id,
                name,
                email,
                password_hash
            ) VALUES (
                :id,
                :name,
                :email,
                :password_hash
            )
        SQL;

        $statement = $this->pdo->prepare($sql);

        $statement->execute([
            'id' => $user->id()->value(),
            'name' => $user->name()->value(),
            'email' => $user->email()->value(),
            'password_hash' => $user->passwordHash()->value(),
        ]);
    }

    public function findById(UserId $id): ?User
    {
        return null;
    }

    public function findByEmail(Email $email): ?User
    {
        return null;
    }
}
