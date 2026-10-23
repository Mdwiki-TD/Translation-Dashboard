<?php

declare(strict_types=1);

namespace Tests\App\MdwikiSql;

use App\MdwikiSql\Database;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Database::class)]
class DatabaseTest extends TestCase
{
    public function testDatabaseUnconnectedOperationsReturnSafeDefaults(): void
    {
        $db = new Database('NON_EXISTENT_DB_NAME');

        $this->assertSame([], $db->fetchQuery('SELECT * FROM test'));
        $this->assertFalse($db->executeQuery('UPDATE test SET col = 1'));
        $this->assertFalse($db->beginTransaction());
        $this->assertFalse($db->commit());
        $this->assertFalse($db->rollback());
        $this->assertFalse($db->inTransaction());
    }

    public function testDisableFullGroupByModeWhenUnconnected(): void
    {
        $db = new Database('NON_EXISTENT_DB_NAME');
        // Should handle gracefully without throwing
        $db->disableFullGroupByMode('SELECT col1, count(*) FROM test GROUP BY col1');
        $this->assertFalse($db->inTransaction());
    }
}
