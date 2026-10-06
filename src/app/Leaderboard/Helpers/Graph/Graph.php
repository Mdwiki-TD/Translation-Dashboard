<?php

namespace App\Leaderboard\Helpers\Graph;

class Graph
{
    public static function graph_html(string $keys, string $values): string
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

    public static function print_graph_for_table(array $table): string
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

        return self::graph_html($ms, $cs);
    }
}
