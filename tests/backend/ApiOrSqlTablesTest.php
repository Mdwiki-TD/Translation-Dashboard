<?php

namespace Tests\Backend\ApiOrSql;

use PHPUnit\Framework\TestCase;
use App\SQLorAPI\QidsTable;
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
        $this->assertEquals("https://mdwikicx.toolforge.org/w/index.php", (SettingsTable::getInstance())->getEndpoint());
    }

    public function testLeaderboardTableMakeSqlQuery(): void
    {
        $res = (LeaderboardTable::getInstance())->makeSqlQuery('2023', 'admin', 'RTT');
        $this->assertStringContainsString('u.user_group = ?', $res['query']);
        $this->assertStringContainsString('YEAR(p.pupdate) = ?', $res['query']);
        $this->assertStringContainsString('p.cat = ?', $res['query']);
        $this->assertEquals(['admin', '2023', 'RTT'], $res['params']);
    }

    public function testLeaderboardTableMakeApiParams(): void
    {
        $params = (LeaderboardTable::getInstance())->makeApiParams('2023', 'admin', 'RTT');
        $this->assertEquals([
            'get' => 'leaderboard_table',
            'year' => '2023',
            'user_group' => 'admin',
            'cat' => 'RTT',
        ], $params);
    }

    public function testLeaderboardTableTopQuery(): void
    {
        $queryUser = (LeaderboardTable::getInstance())->topQuery('user');
        $this->assertStringContainsString('SELECT', $queryUser);
        $this->assertStringContainsString('p.user', $queryUser);

        $queryLang = (LeaderboardTable::getInstance())->topQuery('lang');
        $this->assertStringContainsString('p.lang', $queryLang);
    }

    public function testTitlesTableGetQids(): void
    {
        $res = (QidsTable::getInstance())->getQidsForList(['nonexistent_title_xyz']);
        $this->assertArrayHasKey('with_qids', $res);
        $this->assertArrayHasKey('no_qids', $res);
        $this->assertContains('nonexistent_title_xyz', $res['no_qids']);
    }

    public function testResetCaches(): void
    {
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
