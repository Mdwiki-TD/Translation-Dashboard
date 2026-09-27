<?php

declare(strict_types=1);

namespace Tests\Leaderboard;

use PHPUnit\Framework\TestCase;
use App\Leaderboard\Index\MainLeaderboard;
use App\Leaderboard\Users\UsersLeaderboard;
use App\Leaderboard\Langs\LangsLeaderboard;
use App\Leaderboard\IndexJs\IndexJsLeaderboard;
use App\Leaderboard\LeaderboardRouter;
use function App\Leaderboard\Index\main_leaderboard;
use function App\Leaderboard\Users\users_html;
use function App\Leaderboard\Langs\langs_html;

class LeaderboardTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        $baseDir = dirname(__DIR__, 2) . '/src/app/leaderboard';

        ob_start();
        require_once $baseDir . '/main.php';
        require_once $baseDir . '/users.php';
        require_once $baseDir . '/langs.php';
        require_once $baseDir . '/index_js.php';
        require_once $baseDir . '/index.php';
        ob_end_clean();
    }

    public function testMainLeaderboardRender(): void
    {
        $mainLeaderboard = new MainLeaderboard();
        $html = $mainLeaderboard->render('all', 'all', 'all', [], false, '');

        $this->assertStringContainsString('Leaderboard', $html);
        $this->assertStringContainsString('Numbers', $html);
        $this->assertStringContainsString('Top users by number of translation', $html);
        $this->assertStringContainsString('Top languages by number of Articles', $html);
    }

    public function testMainLeaderboardFunctionWrapper(): void
    {
        $html = main_leaderboard('2024', 'all', 'all', [], false, '');

        $this->assertStringContainsString('Leaderboard', $html);
    }

    public function testUsersLeaderboardRender(): void
    {
        $usersLeaderboard = new UsersLeaderboard();
        $html = $usersLeaderboard->render(
            'en',
            '2024',
            'all',
            'TestUser',
            'TestUser',
            'TestUser',
            [],
            [],
            'https://example.org'
        );

        $this->assertStringContainsString('User:', $html);
        $this->assertStringContainsString('TestUser', $html);
        $this->assertStringContainsString('Translations in process', $html);
    }

    public function testUsersLeaderboardFunctionWrapper(): void
    {
        $html = users_html(
            'en',
            '2024',
            'all',
            'TestUser',
            'TestUser',
            'TestUser',
            [],
            [],
            'https://example.org'
        );

        $this->assertStringContainsString('User:', $html);
    }

    public function testLangsLeaderboardRender(): void
    {
        $langsLeaderboard = new LangsLeaderboard();
        $html = $langsLeaderboard->render(
            'en',
            '2024',
            'all',
            [],
            [],
            'https://example.org'
        );

        $this->assertStringContainsString('Language:', $html);
        $this->assertStringContainsString('Translations in process', $html);
    }

    public function testLangsLeaderboardFunctionWrapper(): void
    {
        $html = langs_html(
            'ar',
            '2024',
            'all',
            [],
            [],
            'https://example.org'
        );

        $this->assertStringContainsString('Language:', $html);
    }

    public function testIndexJsLeaderboardRender(): void
    {
        $indexJs = new IndexJsLeaderboard();

        ob_start();
        $indexJs->render();
        $output = ob_get_clean();

        $this->assertStringContainsString('Topusers', $output);
        $this->assertStringContainsString('Toplangs', $output);
    }

    public function testLeaderboardRouterHandleRequest(): void
    {
        $_GET['get'] = 'camps';
        $_GET['camps'] = '1';

        $router = new LeaderboardRouter();

        ob_start();
        $router->handleRequest();
        $output = ob_get_clean();

        $this->assertStringContainsString('camps', strtolower($output));

        unset($_GET['get'], $_GET['camps']);
    }
}
