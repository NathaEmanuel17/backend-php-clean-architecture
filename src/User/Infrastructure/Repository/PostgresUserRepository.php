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
                created_at,
                updated_at,
                deleted_at
            ) VALUES (
                :id,
                :name,
                :email,
                :password_hash,
                :created_at,
                :updated_at,
                :deleted_at
            )
            ON CONFLICT (id) DO UPDATE SET
                name = EXCLUDED.name,
                email = EXCLUDED.email,
                password_hash = EXCLUDED.password_hash,
                updated_at = EXCLUDED.updated_at,
                deleted_at = EXCLUDED.deleted_at
        SQL;

        $statement = $this->pdo->prepare($sql);

        $statement->execute([
            'id' => $user->id()->value(),
            'name' => $user->name()->value(),
            'email' => $user->email()->value(),
            'password_hash' => $user->passwordHash()->value(),
            'created_at' => $user->createdAt()->format('Y-m-d H:i:s.u'),
            'updated_at' => $user->updatedAt()->format('Y-m-d H:i:s.u'),
            'deleted_at' => $user->deletedAt()?->format('Y-m-d H:i:s.u'),
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
                password_hash,
                created_at,
                updated_at,
                deleted_at
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
                password_hash,
                created_at,
                updated_at,
                deleted_at
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

        /** @var array<string, string|null>|false $row */
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        /** @var array{
         *     id: string,
         *     name: string,
         *     email: string,
         *     password_hash: string,
         *     created_at: string,
         *     updated_at: string,
         *     deleted_at: string|null
         * } $row
         */
        return $this->userMapper->toEntity($row);
    }

    /**
 * @return list<User>
 */
    public function findAll(): array
    {
        $statement = $this->pdo->query(
            '
        SELECT
            id,
            name,
            email,
            password_hash,
            created_at,
            updated_at,
            deleted_at
        FROM users
        WHERE deleted_at IS NULL
        ORDER BY created_at DESC
        '
        );

        if ($statement === false) {
            return [];
        }

        /** @var list<array{
         *     id: string,
         *     name: string,
         *     email: string,
         *     password_hash: string,
         *     created_at: string,
         *     updated_at: string,
         *     deleted_at: string|null
         * }> $rows
         */
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            fn (array $row): User => $this->userMapper->toEntity($row),
            $rows
        );
    }
}
