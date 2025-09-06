<?php
namespace Leaderboard\Subs\LeadHelp;

/**
 * Produce simple table HTML strings that include the type ('translations' or 'pending')
 * and echo the mainlang for visibility in assertions.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
function make_langs_lead(array $data, string $type, array $table_of_views, string $mainlang) : array {
    $rows = '';
    foreach ($data as $row) {
        $title = htmlspecialchars($row['title'] ?? '', ENT_QUOTES, 'UTF-8');
        $views = htmlspecialchars((string)($row['views'] ?? ''), ENT_QUOTES, 'UTF-8');
        $rows .= "<tr><td>{$title}</td><td>{$views}</td></tr>";
    }
    $table = "<table data-type='{$type}' data-lang='{$mainlang}'>" . $rows . "</table>";
    // Return [table1, main_table] per SUT expectation when type==='translations'
    if ($type === 'translations') {
        // Left side summary and main table
        return ["<div class='summary {$type}'></div>", $table];
    }
    // For 'pending', the SUT ignores the first element with [$_, $table_pnd]
    return ["", $table];
}