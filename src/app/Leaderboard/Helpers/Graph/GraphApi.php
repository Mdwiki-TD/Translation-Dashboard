<?php

namespace App\Leaderboard\Helpers\Graph;

class GraphApi
{
    public static function graph_new_html($params, $no_card = false)
    {
        $graph_id = 'chart_' . uniqid();

        $canvas = <<<HTML
            <div class="position-relative">
                <canvas id="$graph_id" height="200" class="invert-on-dark"></canvas>
            </div>
        HTML;

        $graph = <<<HTML
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

        if ($no_card) {
            $graph = $canvas;
        }

        $graph .= '<script src="/Translation_Dashboard/js/graph_api.js"></script>';

        $graph .= "<script>graph_js_params('$graph_id', " . json_encode($params) . ")</script>";

        return "\n" . $graph . "\n";
    }

    public static function print_graph_api($tab, $no_card = false)
    {
        return self::graph_new_html($tab, $no_card);
    }

    public static function print_graph_tab_2_new()
    {
        $g = self::graph_new_html([]);

        return <<<HTML
            <div class="container">
                <div class="col-md-10">
                    $g
                </div>
            </div>
        HTML;
    }
}
