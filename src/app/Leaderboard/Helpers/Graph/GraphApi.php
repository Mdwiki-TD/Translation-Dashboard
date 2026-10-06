<?php

namespace App\Leaderboard\Helpers\Graph;

class GraphApi
{
    public static function graph_new_html(array $params): string
    {
        $graph_id = 'chart_' . uniqid();

        $graph = <<<HTML
            <div class="card">
                <div class="card-header aligncenter" style="font-weight:bold;">
                    <!-- <a href="/Translation_Dashboard/leaderboard.php?graph=1">Translation by month</a> -->
                    Translation by month
                </div>
                <div class="card-body1 card5px">
                    <div class="position-relative">
                        <canvas id="$graph_id" height="200" class="invert-on-dark"></canvas>
                    </div>
                </div>
            </div>
        HTML;

        $graph .= "<script>graph_js_params('$graph_id', " . json_encode($params) . ")</script>";

        return "\n" . $graph . "\n";
    }

    public static function renderGraph(): string
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
