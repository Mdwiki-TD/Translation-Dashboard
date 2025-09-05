<?php
namespace Leaderboard\Subs\FilterForm;

/**
 * Compose the lead row HTML; return a wrapper to assert header and hidden filter data.
 */
function lead_row($table1, $graph, string $headerHtml, array $filterData, string $scope) {
    $attrs = htmlspecialchars(json_encode($filterData), ENT_QUOTES, 'UTF-8');
    $graphAttr = htmlspecialchars(json_encode($graph), ENT_QUOTES, 'UTF-8');
    return "<div class='lead-row' data-scope='{$scope}' data-filter='{$attrs}' data-graph='{$graphAttr}'>{$headerHtml}{$table1}</div>";
}