<script src="/Translation_Dashboard/js/g.js"></script>
<?PHP
//---
function print_graph() {
        
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
        $m = $Taab['m'];
        $c = $Taab['c'];
        //---
        $ms .= "'$m',";
        $cs .= "$c,";
    }
    $ms = substr($ms, 0, -1);
    $cs = substr($cs, 0, -1);
    //---
    $graph =  <<<HTML
        <div class="card">
            <div class="card-header aligncenter" style="font-weight:bold;">
                <a href="/Translation_Dashboard/leaderboard.php?graph=1">Translation by month</a>
            </div>
            <div class="card-body">
                <div class="position-relative mb-4">
                    <canvas id="chart1" height="200"></canvas>
                </div>
            </div>
        </div>
        <script>
            graph_js(
                [$ms],
                [$cs],
                "chart1"
            )
        </script>
    HTML;
    return $graph;
}

function print_graph_tab() {
    $g = print_graph();
    echo <<<HTML
        <div class="container">
            <div class="col-md-10">
                $g
            </div>
        </div>
    HTML;
}