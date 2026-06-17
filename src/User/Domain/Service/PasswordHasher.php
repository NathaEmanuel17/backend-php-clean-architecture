<?php

declare(strict_types=1);

namespace App\User\Domain\Service;

use App\User\Domain\ValueObject\PasswordHash;

interface PasswordHasher
{
    public function hash(string $plainPassword): PasswordHash;
}
