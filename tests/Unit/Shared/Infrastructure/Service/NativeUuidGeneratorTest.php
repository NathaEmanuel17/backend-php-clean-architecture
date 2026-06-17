<?php

declare(strict_types=1);

namespace Tests\Unit\Shared\Infrastructure\Service;

use App\Shared\Domain\Service\UuidGenerator;
use App\Shared\Infrastructure\Service\NativeUuidGenerator;
use PHPUnit\Framework\TestCase;

final class NativeUuidGeneratorTest extends TestCase
{
    public function testShouldGenerateValidUuid(): void
    {
        $generator = new NativeUuidGenerator();

        $uuid = $generator->generate();

        self::assertInstanceOf(UuidGenerator::class, $generator);
        self::assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $uuid
        );
    }
}
