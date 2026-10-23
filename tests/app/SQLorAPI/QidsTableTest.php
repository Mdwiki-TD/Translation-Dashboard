<?php

declare(strict_types=1);

namespace Tests\App\SQLorAPI;

use App\MdwikiSql\Database;
use App\SQLorAPI\ApiOrSqlService;
use App\SQLorAPI\QidsTable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(QidsTable::class)]
class QidsTableTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        QidsTable::resetCache();
    }

    protected function tearDown(): void
    {
        QidsTable::resetCache();
        parent::tearDown();
    }

    public function testGetTitlesQidsAndGetQidsForList(): void
    {
        $service = $this->createMock(ApiOrSqlService::class);
        $service->expects($this->once())
            ->method('superFunction')
            ->willReturn([
                ['title' => 'Article1', 'qid' => 'Q100'],
                ['title' => 'Article2', 'qid' => 'Q200']
            ]);

        $table = new QidsTable($service);
        $qids = $table->getTitlesQids();

        $this->assertSame(['Article1' => 'Q100', 'Article2' => 'Q200'], $qids);

        $classified = $table->getQidsForList(['Article1', 'Article3']);
        $this->assertSame(['Article1' => 'Q100'], $classified['with_qids']);
        $this->assertSame(['Article3'], $classified['no_qids']);
    }

    public function testGetQidsRowAndByTitle(): void
    {
        $service = $this->createMock(ApiOrSqlService::class);
        $table = new QidsTable($service);

        $db = $this->createMock(Database::class);
        $db->expects($this->once())
            ->method('fetchQuery')
            ->with('SELECT * FROM qids WHERE title = ?', ['Article1'])
            ->willReturn([['title' => 'Article1', 'qid' => 'Q100']]);

        $table->db = $db;

        $row = $table->get_qid_row('title', 'Article1', 'qids');
        $this->assertSame(['title' => 'Article1', 'qid' => 'Q100'], $row);

        $this->assertNull($table->get_qid_row('invalid_col', 'val'));
    }
}
