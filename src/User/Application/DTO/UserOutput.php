<?php

declare(strict_types=1);

namespace App\User\Application\DTO;

final readonly class UserOutput
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
    ) {
    }
}
