<?php

declare(strict_types=1);

namespace App\User\Application\Command;

final readonly class CreateUserCommand
{
    public function __construct(
        public string $name,
        public string $email,
        public string $plainPassword,
    ) {
        
    }
}
