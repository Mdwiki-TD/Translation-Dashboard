<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use function SQLorAPI\Funcs\coordinator_active_index;
use function SQLorAPI\Funcs\normalize_coordinator_rows;

final class CoordinatorFallbackTest extends TestCase
{
    public function testNormalizeCoordinatorRowsAddsDefaultActive(): void
    {
        $rows = [
            ['user' => 'Alice'],
            ['user' => 'Bob', 'active' => '0'],
            ['user' => 'Carol', 'active' => 1],
        ];

        $normalized = normalize_coordinator_rows($rows);

        $this->assertSame(1, $normalized[0]['active']);
        $this->assertSame(0, $normalized[1]['active']);
        $this->assertSame(1, $normalized[2]['active']);
    }

    public function testCoordinatorActiveIndexTreatsMissingActiveAsActive(): void
    {
        $rows = [
            ['user' => 'Alice'],
            ['user' => 'Bob', 'active' => 0],
        ];

        $index = coordinator_active_index($rows);

        $this->assertSame([
            'Alice' => 1,
            'Bob' => 0,
        ], $index);
    }
}
