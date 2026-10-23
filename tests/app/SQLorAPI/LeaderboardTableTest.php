<?php

declare(strict_types=1);

namespace Tests\App\SQLorAPI;

use App\MdwikiSql\Database;
use App\SQLorAPI\ApiOrSqlService;
use App\SQLorAPI\LeaderboardTable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(LeaderboardTable::class)]
class LeaderboardTableTest extends TestCase
{
    public function testMakeSqlQueryAndApiParams(): void
    {
        $db = $this->createMock(Database::class);
        $service = new ApiOrSqlService($db, false);
        $table = new LeaderboardTable($service);

        $sqlData = $table->makeSqlQuery('2023', 'admin', 'RTT');
        $this->assertStringContainsString('u.user_group = ?', $sqlData['query']);
        $this->assertSame(['admin', '2023', 'RTT'], $sqlData['params']);

        $apiParams = $table->makeApiParams('2023', 'admin', 'RTT');
        $this->assertSame([
            'get' => 'leaderboard_table',
            'year' => '2023',
            'user_group' => 'admin',
            'cat' => 'RTT'
        ], $apiParams);
    }

    public function testGetTopLangOfUsers(): void
    {
        $db = $this->createMock(Database::class);
        $service = new ApiOrSqlService($db, false);

        $table = new LeaderboardTable($service);
        $topLangs = $table->getTopLangOfUsers(['user1', 'user2']);

        $this->assertSame([], $topLangs);
    }

    public function testGetTopUsersAndLangs(): void
    {
        $db = $this->createMock(Database::class);
        $service = new ApiOrSqlService($db, false);

        $table = new LeaderboardTable($service);

        $topUsers = $table->getTopUsers('2023', 'admin', 'RTT');
        $this->assertSame([], $topUsers);

        $topLangs = $table->getTopLangs('2023', 'admin', 'RTT');
        $this->assertSame([], $topLangs);
    }

    public function testGetGraphData(): void
    {
        $db = $this->createMock(Database::class);
        $service = new ApiOrSqlService($db, false);

        $table = new LeaderboardTable($service);
        $graphData = $table->getGraphData('ar', 'user1', '2023', '01', 'RTT');

        $this->assertSame([], $graphData['labels']);
        $this->assertSame([], $graphData['counts']);
    }
}
