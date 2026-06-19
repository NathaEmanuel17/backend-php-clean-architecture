<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Repository;

use App\User\Domain\Entity\User;
use App\User\Domain\Repository\UserRepository;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\UserId;
use App\User\Infrastructure\Mapper\UserMapper;
use PDO;

final readonly class PostgresUserRepository implements UserRepository
{
    public function __construct(
        private PDO $pdo,
        private UserMapper $userMapper = new UserMapper(),
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
        $statement = $this->pdo->prepare(
            '
            SELECT
                id,
                name,
                email,
                password_hash
            FROM users
            WHERE id = :id
            AND deleted_at IS NULL
            '
        );

        $statement->execute([
            'id' => $id->value(),
        ]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if (!is_array($row)) {
            return null;
        }

        /** @var array{
         *     id: string,
         *     name: string,
         *     email: string,
         *     password_hash: string
         * } $row
         */
        return $this->userMapper->toEntity($row);
    }

    public function findByEmail(Email $email): ?User
    {
        $statement = $this->pdo->prepare(
            '
            SELECT
                id,
                name,
                email,
                password_hash
            FROM users
            WHERE email = :email
            AND deleted_at IS NULL
            '
        );

        $statement->execute([
            'email' => $email->value(),
        ]);

        /** @var array<string, string>|false $row */
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        /** @var array{
         *     id: string,
         *     name: string,
         *     email: string,
         *     password_hash: string
         * } $row
         */
        return $this->userMapper->toEntity($row);
    }
}
