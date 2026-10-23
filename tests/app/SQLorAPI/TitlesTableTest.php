<?php

declare(strict_types=1);

namespace Tests\App\SQLorAPI;

use App\SQLorAPI\ApiOrSqlService;
use App\SQLorAPI\TitlesTable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TitlesTable::class)]
class TitlesTableTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        TitlesTable::resetCache();
    }

    protected function tearDown(): void
    {
        TitlesTable::resetCache();
        parent::tearDown();
    }

    public function testGetTitlesInfosAndProjectsAndLangs(): void
    {
        $service = $this->createMock(ApiOrSqlService::class);
        $service->expects($this->exactly(3))
            ->method('superFunction')
            ->willReturnOnConsecutiveCalls(
                [['title' => 'T1', 'qid' => 'Q1']],
                [['g_id' => '1', 'g_title' => 'P1']],
                [['code' => 'en', 'name' => 'English']]
            );

        $table = new TitlesTable($service);

        $infos = $table->getTitlesInfos();
        $this->assertSame([['title' => 'T1', 'qid' => 'Q1']], $infos);

        $projects = $table->getProjects();
        $this->assertSame([['g_id' => '1', 'g_title' => 'P1']], $projects);

        $langs = $table->getLangs();
        $this->assertSame(['en' => ['code' => 'en', 'name' => 'English']], $langs);
    }
}
