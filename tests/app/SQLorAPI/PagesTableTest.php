<?php

declare(strict_types=1);

namespace Tests\App\SQLorAPI;

use App\MdwikiSql\Database;
use App\SQLorAPI\ApiOrSqlService;
use App\SQLorAPI\PagesTable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PagesTable::class)]
class PagesTableTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        PagesTable::resetCache();
    }

    protected function tearDown(): void
    {
        PagesTable::resetCache();
        parent::tearDown();
    }

    public function testGetLangPagesByCat(): void
    {
        $db = $this->createMock(Database::class);
        $service = new ApiOrSqlService($db, false);

        $table = new PagesTable($service);
        $res = $table->getLangPagesByCat('ar', 'RTT');

        $this->assertSame([], $res);
    }

    public function testGetCountPagesAndNotEmpty(): void
    {
        $db = $this->createMock(Database::class);
        $service = new ApiOrSqlService($db, false);

        $table = new PagesTable($service);

        $c1 = $table->getCountPages();
        $this->assertSame([], $c1);

        $c2 = $table->getCountPagesNotEmpty();
        $this->assertSame([], $c2);
    }

    public function testGetTotalTranslationsCount(): void
    {
        $db = $this->createMock(Database::class);
        $service = new ApiOrSqlService($db, false);

        $table = new PagesTable($service);
        $count = $table->getTotalTranslationsCount('ar', 'pages');

        $this->assertSame(0, $count);
    }
}
