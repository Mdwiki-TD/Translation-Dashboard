<?php

declare(strict_types=1);

namespace Tests\Leaderboard;

use PHPUnit\Framework\TestCase;
use App\Leaderboard\MainLeaderboard;
use App\Leaderboard\UsersLeaderboard;
use App\Leaderboard\LangsLeaderboard;
use App\Controllers\LeaderboardJsController;
use App\Controllers\LeaderboardController;

class LeaderboardTest extends TestCase
{

    public function testMainLeaderboardRender(): void
    {
        $mainLeaderboard = new MainLeaderboard('all', 'all', 'all', '');
        $html = $mainLeaderboard->render([], false);

        $this->assertStringContainsString('Leaderboard', $html);
        $this->assertStringContainsString('Numbers', $html);
        $this->assertStringContainsString('Top users by number of translation', $html);
        $this->assertStringContainsString('Top languages by number of Articles', $html);
    }

    public function testUsersLeaderboardRender(): void
    {
        $usersLeaderboard = new UsersLeaderboard('TestUser', 'all');
        $html = $usersLeaderboard->render(
            'en',
            '2024',
            'TestUser',
            [],
            []
        );

        $this->assertStringContainsString('User:', $html);
        $this->assertStringContainsString('TestUser', $html);
        $this->assertStringContainsString('Translations in process', $html);
    }

    public function testLangsLeaderboardRender(): void
    {
        $langsLeaderboard = new LangsLeaderboard('all');
        $html = $langsLeaderboard->render(
            'en',
            '2024',
            [],
            []
        );

        $this->assertStringContainsString('Language:', $html);
        $this->assertStringContainsString('Translations in process', $html);
    }

    public function testIndexJsLeaderboardRender(): void
    {
        $indexJs = new LeaderboardJsController();

        ob_start();
        $indexJs->handleRequest();
        $output = ob_get_clean();

        $this->assertStringContainsString('Topusers', $output);
        $this->assertStringContainsString('Toplangs', $output);
    }

    public function testLeaderboardRouterHandleRequest(): void
    {
        $_GET['get'] = 'camps';
        $_GET['camps'] = '1';

        $router = new LeaderboardController();

        ob_start();
        $router->handleRequest();
        $output = ob_get_clean();

        $this->assertStringContainsString('camps', strtolower($output));

        unset($_GET['get'], $_GET['camps']);
    }
}
