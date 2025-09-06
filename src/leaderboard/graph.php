<?PHP

namespace Leaderboard\Graph;
/*
Usage:

use function Leaderboard\Graph\graph_html;
use function Leaderboard\Graph\print_graph_from_sql;
use function Leaderboard\Graph\print_graph_for_table;
use function Leaderboard\Graph\print_graph_tab;

*/
//---
use function SQLorAPI\Funcs\get_graph_data;
// ---
function graph_html($keys, $values, $no_card = false)
{
    // ---
    $graph_id = 'chart_' . uniqid();
    // ---
    $canvas = <<<HTML
        <div class="position-relative">
            <canvas id="$graph_id" height="200" class="invert-on-dark"></canvas>
        </div>
    HTML;
    //---
    $graph =  <<<HTML
        <div class="card">
            <div class="card-header " style="font-weight:bold;">
                Translation by month
                <div class="card-tools">
                    <button type="button" class="btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
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
    $graph .=  <<<HTML
        <script>
            graph_js(
                [$keys],
                [$values],
                "$graph_id"
            )
        </script>
    HTML;
    return $graph;
}
function print_graph_for_table($table, $no_card = false)
{
    //---
    // sort $table by keys
    ksort($table);
    //---
    $ms = "";
    $cs = "";
    //---
    foreach ($table as $key => $value) {
        //---
        $ms .= "'$key',";
        $cs .= "$value,";
    }
    $ms = substr($ms, 0, -1);
    $cs = substr($cs, 0, -1);
    //---
    $graph = graph_html($ms, $cs, $no_card = $no_card);
    //---
    return $graph;
}

function print_graph_from_sql()
{
    //---
    $data = get_graph_data();
    //---
    $ms = "";
    $cs = "";
    //---
    foreach ($data as $yhu => $Taab) {
        //---
        $m = $Taab['m'] ?? "";
        $c = $Taab['c'] ?? "";
        //---
        $ms .= "'$m',";
        $cs .= "$c,";
    }
    $ms = substr($ms, 0, -1);
    $cs = substr($cs, 0, -1);
    //---
    $graph =  graph_html($ms, $cs);
    //---
    return $graph;
}

function print_graph_tab()
{
    $g = print_graph_from_sql();
    return <<<HTML
        <div class="container">
            <div class="col-md-10">
                $g
            </div>
        </div>
    HTML;
}
