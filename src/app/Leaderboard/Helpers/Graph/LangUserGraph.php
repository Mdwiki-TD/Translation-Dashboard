<?php

namespace App\Leaderboard\Helpers\Graph;

class LangUserGraph
{

    public static function graphData(array $data): string
    {
        $graph_id = 'chart_' . uniqid();
        $row = json_encode($data);

        $text = <<<HTML
            <canvas id="$graph_id" height="100" width="200" class="invert-on-dark"></canvas>
            <script>
                render_graph($row, '$graph_id')
            </script>
        HTML;

        return $text;
    }
}
