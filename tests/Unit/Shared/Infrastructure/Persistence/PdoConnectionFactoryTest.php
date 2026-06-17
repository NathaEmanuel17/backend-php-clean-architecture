<?php

declare(strict_types=1);

namespace Tests\Unit\Shared\Infrastructure\Persistence;

use App\Shared\Infrastructure\Persistence\PdoConnectionFactory;
use PDO;
use PHPUnit\Framework\TestCase;

final class PdoConnectionFactoryTest extends TestCase
{
    public function testShouldCreatePdoInstance(): void
    {
        $pdo = PdoConnectionFactory::create();

        self::assertInstanceOf(PDO::class, $pdo);
    }
}
