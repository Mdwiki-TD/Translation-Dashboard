<?php

namespace App\Leaderboard\Helpers\Graph;

class Graph
{
    public static function graph_html($keys, $values, $no_card = false)
    {
        $graph_id = 'chart_' . uniqid();

        $canvas = <<<HTML
            <div class="position-relative">
                <canvas id="$graph_id" height="200" class="invert-on-dark"></canvas>
            </div>
        HTML;

        $graph = <<<HTML
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

        if ($no_card) {
            $graph = $canvas;
        }

        $graph .= <<<HTML
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

    public static function print_graph_for_table($table, $no_card = false)
    {
        ksort($table);

        $ms = "";
        $cs = "";

        foreach ($table as $key => $value) {
            $ms .= "'$key',";
            $cs .= "$value,";
        }
        $ms = substr($ms, 0, -1);
        $cs = substr($cs, 0, -1);

        return self::graph_html($ms, $cs, $no_card);
    }

    public static function print_graph_from_sql($data)
    {
        $ms = "";
        $cs = "";

        foreach ($data as $yhu => $Taab) {
            $m = $Taab['m'] ?? "";
            $c = $Taab['c'] ?? "";

            $ms .= "'$m',";
            $cs .= "$c,";
        }
        $ms = substr($ms, 0, -1);
        $cs = substr($cs, 0, -1);

        return self::graph_html($ms, $cs);
    }

    public static function print_graph_tab($data)
    {
        $g = self::print_graph_from_sql($data);
        return <<<HTML
            <div class="container">
                <div class="col-md-10">
                    $g
                </div>
            </div>
        HTML;
    }
}
