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
            password_hash,
            updated_at
        ) VALUES (
            :id,
            :name,
            :email,
            :password_hash,
            CURRENT_TIMESTAMP
        )
        ON CONFLICT (id) DO UPDATE SET
            name = EXCLUDED.name,
            email = EXCLUDED.email,
            password_hash = EXCLUDED.password_hash,
            updated_at = CURRENT_TIMESTAMP
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
        return $this->findOneBy(
            '
        SELECT
            id,
            name,
            email,
            password_hash
        FROM users
        WHERE id = :id
          AND deleted_at IS NULL
        ',
            [
                'id' => $id->value(),
            ]
        );
    }

    public function findByEmail(Email $email): ?User
    {
        return $this->findOneBy(
            '
        SELECT
            id,
            name,
            email,
            password_hash
        FROM users
        WHERE email = :email
          AND deleted_at IS NULL
        ',
            [
                'email' => $email->value(),
            ]
        );
    }

    /**
     * @param array<string, string> $parameters
     */
    private function findOneBy(string $sql, array $parameters): ?User
    {
        $statement = $this->pdo->prepare($sql);

        $statement->execute($parameters);

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
