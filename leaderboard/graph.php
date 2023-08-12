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
    $graph =  <<<HTML
    <div class="card">
        <div class="card-header aligncenter" style="font-weight:bold;">
            <a href="/Translation_Dashboard/leaderboard.php?graph=1">Translation by month</a>
        </div>
        <div class="card-body">
            <div class="position-relative mb-4">
                <canvas id="visitors-chart" height="200"></canvas>
            </div>
        </div>
    </div>
    <script>
        var ticksStyle = {
            fontColor: '#495057',
            fontStyle: 'bold'
        }

    HTML;
    //---
    $graph .= "
        var _labels = [$ms];
        var _data = [$cs];
    ";
    $graph .= '
        var mode = "index"
        var intersect = true


        var $visitorsChart = $("#visitors-chart")
        // eslint-disable-next-line no-unused-vars
        var visitorsChart = new Chart($visitorsChart, {
            data: {
                labels: _labels,
                datasets: [{
                    type: "line",
                    data: _data,
                    backgroundColor: "transparent",
                    borderColor: "#007bff",
                    pointBorderColor: "#007bff",
                    pointBackgroundColor: "#007bff",
                    fill: false
                }]
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    mode: mode,
                    intersect: intersect
                },
                hover: {
                    mode: mode,
                    intersect: intersect
                },
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        // display: false,
                        gridLines: {
                            display: true,
                            lineWidth: "4px",
                            color: "rgba(0, 0, 0, .2)",
                            zeroLineColor: "transparent"
                        },
                        ticks: $.extend({
                            beginAtZero: true
                            // suggestedMax: 200
                        }, ticksStyle)
                    }],
                    xAxes: [{
                        display: true,
                        gridLines: {
                            display: false
                        },
                        ticks: ticksStyle
                    }]
                }
            }
        })
    </script>
    ';
    return $graph;
}