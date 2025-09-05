<?php
namespace Leaderboard\Filter;

/**
 * Stub for leaderboard_filter used by the page under test.
 * Returns deterministic HTML so tests can assert its presence.
 */
function leaderboard_filter(string $year, string $user_group, string $camp, string $target): string {
    return "<form id=\"leaderboard_filter\" data-year=\"$year\" data-user_group=\"$user_group\" data-camp=\"$camp\" action=\"$target\"></form>";
}