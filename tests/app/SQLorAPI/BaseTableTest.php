<?php

declare(strict_types=1);

namespace Tests\App\SQLorAPI;

use App\MdwikiSql\Database;
use App\SQLorAPI\ApiOrSqlService;
use App\SQLorAPI\BaseTable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BaseTable::class)]
class BaseTableTest extends TestCase
{
    public function testBaseTableInitializationWithService(): void
    {
        $db = new Database('NON_EXISTENT_DB');
        $service = new ApiOrSqlService($db, false);

        $baseTable = new BaseTable($service);

        $this->assertInstanceOf(Database::class, $baseTable->db);
        $this->assertSame($service, $baseTable->service);
    }

    public function testBaseTableInitializationDefault(): void
    {
        $baseTable = new BaseTable();

        $this->assertInstanceOf(Database::class, $baseTable->db);
        $this->assertInstanceOf(ApiOrSqlService::class, $baseTable->service);
    }
}
