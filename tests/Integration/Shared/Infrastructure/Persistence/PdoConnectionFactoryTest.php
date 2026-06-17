<?php

declare(strict_types=1);

namespace Tests\Integration\Shared\Infrastructure\Persistence;

use App\Shared\Infrastructure\Persistence\PdoConnectionFactory;
use PHPUnit\Framework\TestCase;

final class PdoConnectionFactoryTest extends TestCase
{
    public function testShouldConnectToPostgresDatabase(): void
    {
        $pdo = PdoConnectionFactory::create();

        $statement = $pdo->query('SELECT current_database()');

        self::assertNotFalse($statement);
        self::assertSame('app', $statement->fetchColumn());
    }
}
