<?PHP
//---
if (isset($_REQUEST['test']) || isset($_COOKIE['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};

include_once __DIR__ . '/../header.php';
include_once __DIR__ . '/../results/getcats.php';
include_once __DIR__ . '/../Tables/include.php';
include_once __DIR__ . '/include.php';

use function Leaderboard\Filter\leaderboard_filter;

$year       = $_GET['year'] ?? 'all';
$camp       = $_GET['camp'] ?? 'all';
$user_group = $_GET['project'] ?? $_GET['user_group'] ?? 'all';
//---
$filter_form = leaderboard_filter($year, $user_group, $camp, 'x.php');
echo <<<HTML
    <main id="body">
        <div id="maindiv" class="container-fluid">
            <script src="/Translation_Dashboard/js/g.js"></script>
            <script src="/Translation_Dashboard/js/graph_api.js"></script>
            $filter_form
            <hr />

HTML;
?>
<div class="container-fluid">
    <div class="row g-3">
        <div class="col-md-3">
            <div class="card card2 mb-3">
                <div class="card-header">
                    <span class="card-title" style="font-weight:bold;">
                        Numbers
                    </span>
                    <div style='float: right'>

                    </div>
                    <div class="card-tools">
                        <button type="button" class="btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body1 card2">
                    <table class='table compact table-striped leaderboard_tables'>
                        <thead>
                            <tr>
                                <th class="spannowrap">Type</th>
                                <th>Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><b>Users</b></td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td><b>Articles</b></td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td><b>Words</b></td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td><b>Languages</b></td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td><b>Pageviews</b></td>
                                <td>0</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-header aligncenter" style="font-weight:bold;">
                    Translation by month
                    <div class="card-tools">
                        <button type="button" class="btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body1 card5px">
                    <div class="position-relative">
                        <canvas id="chart09" height="200"></canvas>
                    </div>
                </div>
            </div>
            <script>
                graph_js(
                    ['2025-05', '2025-06'],
                    [10, 100],
                    "chart09"
                )
            </script>
        </div>
        <div class="col-md-5">
            <div class="card card2 mb-3">
                <div class="card-header">
                    <span class="card-title" style="font-weight:bold;">
                        Top users by number of translation
                    </span>
                    <div style='float: right'>
                    </div>
                    <div class="card-tools">
                        <button type="button" class="btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body1 card2">
                    <table class='table compact table-striped leaderboard_tables' id='Topusers'
                        style='margin-top: 0px !important;margin-bottom: 0px !important'>
                        <thead>
                            <tr>
                                <th class="spannowrap">#</th>
                                <th class="spannowrap">User</th>
                                <th>Number</th>
                                <th>Words</th>
                                <th>Pageviews</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card2 mb-3">
                <div class="card-header">
                    <span class="card-title" style="font-weight:bold;">
                        Top languages by number of Articles
                    </span>
                    <div style='float: right'>

                    </div>
                    <div class="card-tools">
                        <button type="button" class="btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body1 card2">
                    <table class='table compact table-striped sortable leaderboard_tables' id='Toplangs'
                        style='margin-top: 0px !important;margin-bottom: 0px !important'>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th class='spannowrap'>Language</th>
                                <th>Count</th>
                                <th>Pageviews</th>

                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td><a href='leaderboard.php?langcode=or'><span data-toggle='tooltip'
                                            title='or'>Odia</span></a></td>
                                <td>1,906</td>
                                <td>1,536,353</td>

                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
</div>
</main>
<script src="/Translation_Dashboard/js/c.js"></script>
<script>
    $('.sortable').DataTable({
        stateSave: true,
        paging: false,
        info: false,
        searching: false
    });
    // ---
    async function get_categories(camp) {
        const response = await fetch('/api.php?get=categories');
        const data = await response.json();

        const campaign_to_categories = {};
        data.results.forEach(item => {
            campaign_to_categories[item.campaign] = item.category;
        });

        return campaign_to_categories[camp] ?? '';
    }
    // ---
    function getFormData(d) {
        d['get'] = 'top_users';
        // ---
        // '/api.php?get=top_users&year=&user_group=&cat=';
        // ---
        const formData = $('#leaderboard_filter').serializeArray();
        formData.forEach(field => {
            if (field.value.trim()) {
                d[field.name] = field.value;
            }
        });
        // ---
        d["cat"] = get_categories(d["camp"]);
    }
    // ---
    $('#Topusers').DataTable({
        stateSave: true,
        paging: false,
        info: false,
        searching: false,
        ajax: {
            url: '/api.php',
            data: getFormData,
            dataSrc: 'results' // المسار داخل JSON الذي يحتوي على الصفوف
        },
        columns: [{ // رقم تسلسلي تلقائي
                data: null,
                title: '#',
                render: function(data, type, row, meta) {
                    return meta.row + 1;
                },
                className: 'dt-center'
            },
            {
                data: 'user',
                title: 'User',
                render: function(data, type) {
                    return `<a href="/Translation_Dashboard/leaderboard.php?user=${data}">${data}</a>`;
                }
            },
            {
                data: 'targets',
                title: 'Number',
                render: function(data) {
                    return Number(data).toLocaleString();
                }
            },
            {
                data: 'words',
                title: 'Words',
                render: function(data) {
                    return Number(data).toLocaleString();
                }
            },
            {
                data: 'views',
                title: 'Pageviews',
                render: function(data) {
                    return Number(data).toLocaleString();
                }
            }
        ]
    });
    // ---
</script>
</body>

</html>
