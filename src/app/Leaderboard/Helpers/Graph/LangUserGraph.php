<?php

namespace App\Leaderboard\Helpers\Graph;

class LangUserGraph
{
    public static function graphDataHtml(array $data): string
    {
        $graph_id = 'chart_' . uniqid();
        $row = json_encode($data);

        $text = <<<HTML
            <canvas id="$graph_id" height="100" width="200" class="invert-on-dark"></canvas>
            <script>
                render_graph('$row', '$graph_id')
            </script>
        HTML;

        return $text;
    }
    public static function graphDataHtmlCard(array $data): string
    {
        $graph_id = 'chart_' . uniqid();

        $graph = <<<HTML
            <div class="card">
                <div class="card-header " style="font-weight:bold;">
                    Translation by month
                    <div class="card-tools">
                        <button type="button" class="btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body1 card5px">
                    <div class="position-relative">
                        <canvas id="$graph_id" height="200" class="invert-on-dark"></canvas>
                    </div>
                </div>
            </div>
        HTML;

        $row = json_encode($data);
        $graph .= <<<HTML
            <script>
                render_graph('$row', '$graph_id')
            </script>
        HTML;
        return $graph;
    }
}
