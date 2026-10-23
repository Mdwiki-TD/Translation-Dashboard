<?php

declare(strict_types=1);

namespace Tests\App\Tables;

use App\Tables\LangsTables;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(LangsTables::class)]
class LangsTablesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        \App\SQLorAPI\TitlesTable::resetCache();
        $this->resetLangsTablesLoadedFlag();
    }

    protected function tearDown(): void
    {
        \App\SQLorAPI\TitlesTable::resetCache();
        $this->resetLangsTablesLoadedFlag();
        parent::tearDown();
    }

    private function resetLangsTablesLoadedFlag(): void
    {
        $ref = new \ReflectionClass(LangsTables::class);
        $prop = $ref->getProperty('already_loaded');
        $prop->setAccessible(true);
        $prop->setValue(null, false);
    }

    public function testLangsTablesLookups(): void
    {
        $service = $this->createMock(\App\SQLorAPI\ApiOrSqlService::class);
        $service->expects($this->once())
            ->method('superFunction')
            ->willReturn([
                'ar' => [
                    'code' => 'ar',
                    'autonym' => 'العربية',
                    'name' => 'Arabic',
                    'redirects' => ''
                ]
            ]);

        $titlesTable = new \App\SQLorAPI\TitlesTable($service);
        $refInstance = new \ReflectionProperty(\App\SQLorAPI\TitlesTable::class, 'instance');
        $refInstance->setAccessible(true);
        $refInstance->setValue(null, $titlesTable);

        $title = LangsTables::get_lang_title('ar');
        $name = LangsTables::get_lang_name('ar');

        $this->assertNotNull($title);
        $this->assertNotNull($name);
        $this->assertStringContainsString('ar', $title);
        $this->assertSame('العربية', $name);
        $this->assertContains('commons', LangsTables::$L_skip_codes);
    }
}
