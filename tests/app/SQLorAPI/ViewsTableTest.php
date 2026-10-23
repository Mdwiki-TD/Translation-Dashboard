<?php

declare(strict_types=1);

namespace Tests\App\SQLorAPI;

use App\MdwikiSql\Database;
use App\SQLorAPI\ApiOrSqlService;
use App\SQLorAPI\ViewsTable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ViewsTable::class)]
class ViewsTableTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        ViewsTable::resetCache();
    }

    protected function tearDown(): void
    {
        ViewsTable::resetCache();
        parent::tearDown();
    }

    public function testGetViewsAndUserViewsAndLangViews(): void
    {
        $db = $this->createMock(Database::class);
        $service = new ApiOrSqlService($db, false);

        $table = new ViewsTable($service);

        $v1 = $table->getViews(2023, 'ar');
        $this->assertSame([], $v1);

        $v2 = $table->getUserViews('user1', 2023, 'ar');
        $this->assertSame([], $v2);

        $v3 = $table->getLangViews('ar', 2023);
        $this->assertSame([], $v3);
    }
}
