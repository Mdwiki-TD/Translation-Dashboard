<?php

namespace App\Leaderboard\Helpers\Graph;

class LangUserGraph
{
    public static function make_table(array $data, int $len): array
    {
        $table = [];

        if (count($data) == 0) {
            return ["" => 0, " " => 0];
        }

        foreach ($data as $tat => $row) {
            $pupdate = $row['pupdate'] ?? "";
            $year = substr($pupdate, 0, $len);

            if (isset($table[$year])) {
                $table[$year] += 1;
            } else {
                $table[$year] = 1;
            }
        }

        if (count($table) == 1) {
            $table[""] = 0;
        }

        ksort($table);

        return $table;
    }

    public static function make_graph_data(array $data): array
    {
        $table = self::make_table($data, -3);

        if (count($table) > 15 && (!isset($_GET['g']))) {
            $table = self::make_table($data, 4);
        }

        $ms = "";
        $cs = "";

        foreach ($table as $key => $value) {
            $ms .= "'$key',";
            $cs .= "$value,";
        }

        $ms = substr($ms, 0, -1);
        $cs = substr($cs, 0, -1);

        return [$ms, $cs, count($table)];
    }

    public static function graph_data_new(array $dd): string
    {
        $graph_id = 'chart_' . uniqid();

        [$keys, $values, $count] = self::make_graph_data($dd);

        $text = <<<HTML
            <canvas id="$graph_id" height="100" width="200" class="invert-on-dark"></canvas>
        HTML;

        if ($count > 0) {
            $text .= <<<HTML
            <script>
                graph_js(
                    [$keys],
                    [$values],
                    "$graph_id"
                )
            </script>
        HTML;
        }

        return $text;
    }
}
