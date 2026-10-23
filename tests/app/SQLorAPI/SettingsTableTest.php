<?php

declare(strict_types=1);

namespace Tests\App\SQLorAPI;

use App\SQLorAPI\ApiOrSqlService;
use App\SQLorAPI\SettingsTable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SettingsTable::class)]
class SettingsTableTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        SettingsTable::resetCache();
    }

    protected function tearDown(): void
    {
        SettingsTable::resetCache();
        parent::tearDown();
    }

    public function testGetLanguageSettings(): void
    {
        $service = $this->createMock(ApiOrSqlService::class);
        $service->expects($this->once())
            ->method('superFunction')
            ->willReturn([['lang_code' => 'ar', 'move_dots' => 1]]);

        $table = new SettingsTable($service);
        $langs = $table->getLanguageSettings();

        $this->assertSame([['lang_code' => 'ar', 'move_dots' => 1]], $langs);
    }
}
