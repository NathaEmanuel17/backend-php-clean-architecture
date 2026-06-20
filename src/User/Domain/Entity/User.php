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
        private DateTimeImmutable $updatedAt,
        private ?DateTimeImmutable $deletedAt,
    ) {
    }

    public static function create(
        UserId $id,
        UserName $name,
        Email $email,
        PasswordHash $passwordHash,
    ): self {
        $now = new DateTimeImmutable();

        return new self(
            $id,
            $name,
            $email,
            $passwordHash,
            $now,
            $now,
            null,
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

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function deletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    public function changeName(UserName $name): void
    {
        $this->name = $name;
        $this->touch();
    }

    public function changeEmail(Email $email): void
    {
        $this->email = $email;
        $this->touch();
    }

    public function changePasswordHash(
        PasswordHash $passwordHash
    ): void {
        $this->passwordHash = $passwordHash;
        $this->touch();
    }

    public function delete(): void
    {
        $now = new DateTimeImmutable();

        $this->deletedAt = $now;
        $this->updatedAt = $now;
    }

    private function touch(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
