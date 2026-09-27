<?php

namespace Tests\Backend\ApiOrSql;

use PHPUnit\Framework\TestCase;
use App\SQLorAPI\Get\ApiOrSqlService;
use App\SQLorAPI\Pages\PagesTable;
use App\SQLorAPI\Views\ViewsTable;
use App\SQLorAPI\Categories\CategoriesTable;
use App\SQLorAPI\InProcess\InProcessTable;
use App\SQLorAPI\Users\UsersTable;
use App\SQLorAPI\Leaderboard\LeaderboardTable;
use App\SQLorAPI\Settings\SettingsTable;
use App\SQLorAPI\Titles\TitlesTable;

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
        $this->assertEquals("https://mdwikicx.toolforge.org/w/index.php", SettingsTable::getEndpoint());
        $this->assertEquals("https://mdwikicx.toolforge.org/w/index.php", \App\SQLorAPI\GetDataTab\get_endpoint());
    }

    public function testLeaderboardTableMakeSqlQuery(): void
    {
        $res = LeaderboardTable::makeSqlQuery('2023', 'admin', 'RTT');
        $this->assertStringContainsString('u.user_group = ?', $res['query']);
        $this->assertStringContainsString('YEAR(p.pupdate) = ?', $res['query']);
        $this->assertStringContainsString('p.cat = ?', $res['query']);
        $this->assertEquals(['admin', '2023', 'RTT'], $res['params']);
    }

    public function testLeaderboardTableMakeApiParams(): void
    {
        $params = LeaderboardTable::makeApiParams('2023', 'admin', 'RTT');
        $this->assertEquals([
            'get' => 'leaderboard_table',
            'year' => '2023',
            'user_group' => 'admin',
            'cat' => 'RTT',
        ], $params);
    }

    public function testLeaderboardTableTopQuery(): void
    {
        $queryUser = LeaderboardTable::topQuery('user');
        $this->assertStringContainsString('SELECT', $queryUser);
        $this->assertStringContainsString('p.user', $queryUser);

        $queryLang = LeaderboardTable::topQuery('lang');
        $this->assertStringContainsString('p.lang', $queryLang);
    }

    public function testTitlesTableGetQids(): void
    {
        $res = TitlesTable::getQids(['nonexistent_title_xyz']);
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
