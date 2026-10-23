<?php

declare(strict_types=1);

namespace Tests\App\SQLorAPI;

use App\MdwikiSql\Database;
use App\SQLorAPI\ApiOrSqlService;
use App\SQLorAPI\InProcessTable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(InProcessTable::class)]
class InProcessTableTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        InProcessTable::resetCache();
    }

    protected function tearDown(): void
    {
        InProcessTable::resetCache();
        parent::tearDown();
    }

    public function testGetProcessDataAndCaching(): void
    {
        $db = $this->createMock(Database::class);
        $service = new ApiOrSqlService($db, false);

        $table = new InProcessTable($service);
        $res1 = $table->getProcessData();
        $res2 = $table->getProcessData();

        $this->assertSame($res1, $res2);
    }

    public function testGetUserProcessNewWithYear(): void
    {
        $db = $this->createMock(Database::class);
        $service = new ApiOrSqlService($db, false);

        $table = new InProcessTable($service);
        $res = $table->getUserProcessNew('user1', '2023');

        $this->assertSame([], $res);
    }

    public function testGetUsersProcessNew(): void
    {
        $db = $this->createMock(Database::class);
        $service = new ApiOrSqlService($db, false);

        $table = new InProcessTable($service);
        $res = $table->getUsersProcessNew();

        $this->assertSame([], $res);
    }

    public function testGetLangInProcessByYearAndGetLangInProcess(): void
    {
        $db = $this->createMock(Database::class);
        $service = new ApiOrSqlService($db, false);

        $table = new InProcessTable($service);

        $res1 = $table->getLangInProcessByYear('en', '2023');
        $this->assertSame([], $res1);

        $res2 = $table->getLangInProcess('en');
        $this->assertSame([], $res2);
    }
}
