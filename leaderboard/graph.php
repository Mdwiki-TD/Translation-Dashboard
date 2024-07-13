<?PHP

namespace Leaderboard\Graph;
/*
Usage:

use function Leaderboard\Graph\graph_html;
use function Leaderboard\Graph\print_graph_from_sql;
use function Leaderboard\Graph\print_graph_for_table;
use function Leaderboard\Graph\print_graph_tab;

*/

echo '<script src="/Translation_Dashboard/js/g.js"></script>';
//---
use function Actions\MdwikiSql\execute_query;
// ---
function graph_html($keys, $values, $id = 'chart1', $no_card = false)
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
    $graph .=  <<<HTML
        <script>
            graph_js(
                [$keys],
                [$values],
                "$id"
            )
        </script>
    HTML;
    return $graph;
}
function print_graph_from_sql($id = 'chart1')
{

    $query = <<<SQL
        SELECT LEFT(pupdate, 7) as m, COUNT(*) as c
        FROM pages
        WHERE target != ''
        GROUP BY LEFT(pupdate, 7)
        ORDER BY LEFT(pupdate, 7) ASC;
    SQL;
    //---
    $ms = "";
    $cs = "";
    //---
    foreach (execute_query($query) as $yhu => $Taab) {
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
    $graph =  graph_html($ms, $cs, $id = $id);
    //---
    return $graph;
}

function print_graph_for_table($table, $id = 'chart1', $no_card = false)
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
    $graph =  graph_html($ms, $cs, $id = $id, $no_card = $no_card);
    //---
    return $graph;
}
function print_graph_tab()
{
    $g = print_graph_from_sql();
    echo <<<HTML
        <div class="container">
            <div class="col-md-10">
                $g
            </div>
        </div>
    HTML;
}
