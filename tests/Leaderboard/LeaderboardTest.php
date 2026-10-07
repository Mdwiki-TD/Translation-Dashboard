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
        $usersLeaderboard = new UsersLeaderboard('TestUser', 'all', '2024', 'Main');
        $html = $usersLeaderboard->render(
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
        $langsLeaderboard = new LangsLeaderboard('all', '2024', 'Main');
        $html = $langsLeaderboard->render(
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

}
