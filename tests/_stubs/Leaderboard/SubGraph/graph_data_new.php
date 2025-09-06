<?php
namespace Leaderboard\SubGraph;

/**
 * Return a simple graph payload; tests can override via global.
 */
function graph_data_new(array $dd) {
    if (isset($GLOBALS['__stub_graph_data_new'])) {
        return $GLOBALS['__stub_graph_data_new'];
    }
    return ['points' => count($dd)];
}