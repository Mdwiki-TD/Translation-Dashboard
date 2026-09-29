<?php

namespace Tests\Backend\ApiOrSql;

use PHPUnit\Framework\TestCase;
use App\SQLorAPI\ApiOrSqlService;
use App\SQLorAPI\PagesTable;
use App\SQLorAPI\ViewsTable;
use App\SQLorAPI\CategoriesTable;
use App\SQLorAPI\InProcessTable;
use App\SQLorAPI\UsersTable;
use App\SQLorAPI\LeaderboardTable;
use App\SQLorAPI\SettingsTable;
use App\SQLorAPI\TitlesTable;

class ApiOrSqlTablesTest extends TestCase
{
    public function testApiOrSqlServiceIsValid(): void
    {
        $this->assertTrue(ApiOrSqlService::isValid('ar'));
        $this->assertFalse(ApiOrSqlService::isValid('all'));
        $this->assertFalse(ApiOrSqlService::isValid(''));
        $this->assertFalse(ApiOrSqlService::isValid(null));
    }

    public function testSettingsTableEndpoint(): void
    {
        $this->assertEquals("https://mdwikicx.toolforge.org/w/index.php", (new SettingsTable())->getEndpoint());
    }

    public function testLeaderboardTableMakeSqlQuery(): void
    {
        $res = (new LeaderboardTable())->makeSqlQuery('2023', 'admin', 'RTT');
        $this->assertStringContainsString('u.user_group = ?', $res['query']);
        $this->assertStringContainsString('YEAR(p.pupdate) = ?', $res['query']);
        $this->assertStringContainsString('p.cat = ?', $res['query']);
        $this->assertEquals(['admin', '2023', 'RTT'], $res['params']);
    }

    public function testLeaderboardTableMakeApiParams(): void
    {
        $params = (new LeaderboardTable())->makeApiParams('2023', 'admin', 'RTT');
        $this->assertEquals([
            'get' => 'leaderboard_table',
            'year' => '2023',
            'user_group' => 'admin',
            'cat' => 'RTT',
        ], $params);
    }

    public function testLeaderboardTableTopQuery(): void
    {
        $queryUser = (new LeaderboardTable())->topQuery('user');
        $this->assertStringContainsString('SELECT', $queryUser);
        $this->assertStringContainsString('p.user', $queryUser);

        $queryLang = (new LeaderboardTable())->topQuery('lang');
        $this->assertStringContainsString('p.lang', $queryLang);
    }

    public function testTitlesTableGetQids(): void
    {
        $res = (new TitlesTable())->getQidsForList(['nonexistent_title_xyz']);
        $this->assertArrayHasKey('with_qids', $res);
        $this->assertArrayHasKey('no_qids', $res);
        $this->assertContains('nonexistent_title_xyz', $res['no_qids']);
    }

    public function testResetCaches(): void
    {
        ApiOrSqlService::resetCache();
        PagesTable::resetCache();
        ViewsTable::resetCache();
        CategoriesTable::resetCache();
        InProcessTable::resetCache();
        UsersTable::resetCache();
        SettingsTable::resetCache();
        TitlesTable::resetCache();

        $this->assertTrue(true);
    }
}
