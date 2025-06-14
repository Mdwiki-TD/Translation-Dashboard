<?PHP

namespace Leaderboard\SubGraph;
/*
Usage:
use function Leaderboard\SubGraph\graph_data_new;
use function Leaderboard\SubGraph\make_graph_data;

*/

function make_table($data, $len)
{
    //---
    $table = [];
    //---
    if (count($data) == 0) {
        return ["" => 0, " " => 0];
    }
    //---
    foreach ($data as $tat => $row) {
        //---
        $pupdate = $row['pupdate'] ?? "";
        $year = substr($pupdate, 0, $len);
        //---
        if (isset($table[$year])) {
            $table[$year] += 1;
        } else {
            $table[$year] = 1;
        }
    }
    //---
    if (count($table) == 1) {
        $table[""] = 0;
    }
    //---
    // sort $table by keys
    ksort($table);
    //---
    // var_export($table);
    //---
    return $table;
}

function make_graph_data($data)
{
    //---
    $table = make_table($data, -3);
    //---
    if (count($table) > 12) {
        $table = make_table($data, 4);
    }
    //---
    $ms = "";
    $cs = "";
    //---
    foreach ($table as $key => $value) {
        //---
        $ms .= "'$key',";
        $cs .= "$value,";
    }
    //---
    $ms = substr($ms, 0, -1);
    $cs = substr($cs, 0, -1);
    //---
    return [$ms, $cs, count($table)];
}

function graph_data_new($dd, $id)
{
    // ---
    [$keys, $values, $count] = make_graph_data($dd);
    //---
    $text = <<<HTML
        <canvas id="$id" height="100" width="200"></canvas>
    HTML;
    //---
    if ($count > 0) {
        //---
        $text .= <<<HTML
        <script>
            graph_js(
                [$keys],
                [$values],
                "$id"
            )
        </script>
    HTML;
    }
    // ---
    return $text;
}
