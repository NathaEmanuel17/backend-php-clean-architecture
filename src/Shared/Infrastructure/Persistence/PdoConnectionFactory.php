<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence;

use PDO;

final readonly class PdoConnectionFactory
{
    public static function create(): PDO
    {
        $host = getenv('DB_HOST') ?: 'database';
        $port = getenv('DB_PORT') ?: '5432';
        $database = getenv('DB_DATABASE') ?: 'app';
        $username = getenv('DB_USERNAME') ?: 'app';
        $password = getenv('DB_PASSWORD') ?: 'secret';

        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s',
            $host,
            $port,
            $database
        );

        return new PDO(
            $dsn,
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }
}
