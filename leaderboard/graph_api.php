<?PHP

namespace Leaderboard\Graph2;
/*
http://localhost:9001/Translation_Dashboard/leaderboard.php?graph_api=1&test=1

Usage:

use function Leaderboard\Graph2\print_graph_tab_2_new;
use function Leaderboard\Graph2\graph_new_html;
use function Leaderboard\Graph2\print_graph_api;

*/

function graph_new_html($params, $id = 'chart1', $no_card = false)
{
    $canvas = <<<HTML
        <div class="position-relative">
            <canvas id="$id" height="200"></canvas>
        </div>
    HTML;
    //---
    $graph =  <<<HTML
        <div class="card">
            <div class="card-header aligncenter" style="font-weight:bold;">
                <!-- <a href="/Translation_Dashboard/leaderboard.php?graph=1">Translation by month</a> -->
                Translation by month
            </div>
            <div class="card-body1 card5px">
                $canvas
            </div>
        </div>
    HTML;
    //---
    if ($no_card) {
        $graph = $canvas;
    }
    //---
    $graph .= '<script src="/Translation_Dashboard/js/graph_api.js"></script>';
    //---
    $graph .= "<script>graph_js_params('$id', " . json_encode($params) . ")</script>";
    //---
    return "\n" . $graph . "\n";
}

function print_graph_api($tab, $id = "", $no_card = false)
{
    // &year=&user_group=&campaign=COVID

    return graph_new_html($tab, $id = $id, $no_card = $no_card);
}

function print_graph_tab_2_new()
{
    // &year=&user_group=&campaign=COVID

    $g = graph_new_html([], $id = "chart09");

    echo <<<HTML
        <div class="container">
            <div class="col-md-10">
                $g
            </div>
        </div>
    HTML;
}
