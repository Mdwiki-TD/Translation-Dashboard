<?php

declare(strict_types=1);

namespace Tests\App\SQLorAPI;

use App\MdwikiSql\Database;
use App\SQLorAPI\ApiOrSqlService;
use App\SQLorAPI\RecentTable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RecentTable::class)]
class RecentTableTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        RecentTable::resetCache();
    }

    protected function tearDown(): void
    {
        RecentTable::resetCache();
        parent::tearDown();
    }

    public function testGetRecentPagesWithViews(): void
    {
        $db = $this->createMock(Database::class);
        $service = new ApiOrSqlService($db, false);

        $table = new RecentTable($service);
        $res = $table->getRecentPagesWithViews('ar');

        $this->assertSame([], $res);
    }

    public function testGetRecentTranslated(): void
    {
        $db = $this->createMock(Database::class);
        $service = new ApiOrSqlService($db, false);

        $table = new RecentTable($service);
        $res = $table->getRecentTranslated('en', 'pages', 10, 0);

        $this->assertSame([], $res);
    }
}
