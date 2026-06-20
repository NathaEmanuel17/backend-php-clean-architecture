<?php

declare(strict_types=1);

namespace App\User\Domain\Entity;

use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\PasswordHash;
use App\User\Domain\ValueObject\UserId;
use App\User\Domain\ValueObject\UserName;
use DateTimeImmutable;

final class User
{
    private function __construct(
        private readonly UserId $id,
        private UserName $name,
        private Email $email,
        private PasswordHash $passwordHash,
        private readonly DateTimeImmutable $createdAt,
    ) {
    }

    public static function create(
        UserId $id,
        UserName $name,
        Email $email,
        PasswordHash $passwordHash,
    ): self {
        return new self(
            $id,
            $name,
            $email,
            $passwordHash,
            new DateTimeImmutable()
        );
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function name(): UserName
    {
        return $this->name;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function passwordHash(): PasswordHash
    {
        return $this->passwordHash;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function changeName(UserName $name): void
    {
        $this->name = $name;
    }

    public function changeEmail(Email $email): void
    {
        $this->email = $email;
    }

    public function changePasswordHash(PasswordHash $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }
}
