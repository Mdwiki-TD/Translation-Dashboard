<?php
namespace Leaderboard\Subs\SubLangs;

/**
 * Return a predictable tables structure for tests.
 * The behavior can be controlled via globals set by tests.
 */
function get_langs_tables(string $mainlang, $year_y) {
    // Allow tests to inject custom data via a global
    $injected = $GLOBALS['__stub_get_langs_tables'] ?? null;

    if (is_callable($injected)) {
        return $injected($mainlang, $year_y);
    }

    // Default: simulate two done translations and one pending
    return [
        'dd' => [
            ['title' => 'A', 'views' => 100],
            ['title' => 'B', 'views' => 50],
        ],
        'dd_Pending' => [
            ['title' => 'C', 'views' => 0],
        ],
        'table_of_views' => ['A' => 100, 'B' => 50, 'C' => 0],
    ];
}