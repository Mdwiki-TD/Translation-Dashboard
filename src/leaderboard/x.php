<?PHP
//---
if (isset($_REQUEST['test']) || isset($_COOKIE['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};

include_once __DIR__ . '/../include_all.php';
include_once __DIR__ . '/../header.php';

use function Leaderboard\Filter\leaderboard_filter;

//---
$year  = strtolower(filter_input(INPUT_GET, 'year', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'all');
$month = strtolower(filter_input(INPUT_GET, 'month', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');
$camp  = strtolower(filter_input(INPUT_GET, 'camp', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'all');

$user_group = filter_input(INPUT_GET, 'project', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
    ?? filter_input(INPUT_GET, 'user_group', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
    ?? 'all';
//---
$user_group = strtolower($user_group);
//---
$filter_form = leaderboard_filter($year, $month, $user_group, $camp, 'x.php');

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
                    <table class='table compact table-striped table_text_left leaderboard_tables'>
                        <thead>
                            <tr>
                                <th class="spannowrap">Type</th>
                                <th>Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><b>Users</b></td>
                                <td><span id="c_user">0</span></td>
                            </tr>
                            <tr>
                                <td><b>Articles</b></td>
                                <td><span id="c_articles">0</span></td>
                            </tr>
                            <tr>
                                <td><b>Words</b></td>
                                <td><span id="c_words">0</span></td>
                            </tr>
                            <tr>
                                <td><b>Languages</b></td>
                                <td><span id="c_lang">0</span></td>
                            </tr>
                            <tr>
                                <td><b>Pageviews</b></td>
                                <td><span id="c_pv">0</span></td>
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
                        <canvas id="chart09" height="200" class="invert-on-dark"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card card2 mb-3">
                <div class="card-header">
                    <span class="card-title" style="font-weight:bold;">
                        Top users by number of translation
                    </span>
                    <div style='float: right'>
                        <button type="button" class="btn-tool" href="#" data-bs-toggle="modal" data-bs-target="#targets">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <div class="card-tools">
                        <button type="button" class="btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body1 card2">
                    <table class='table compact table-striped table_text_left leaderboard_tables' id='Topusers'
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
                    <table class='table compact table-striped table_text_left leaderboard_tables' id='Toplangs'
                        style='margin-top: 0px !important;margin-bottom: 0px !important'>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th class='spannowrap'>Language</th>
                                <th>Count</th>
                                <!-- <th>Words</th> -->
                                <th>Pageviews</th>
                            </tr>
                        </thead>
                        <tbody>
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
    // when page ready
    $(document).ready(async function() {
        $('.sortable').DataTable({
            stateSave: true,
            paging: false,
            info: false,
            searching: false
        });
        // ---
        const campaign_to_categories = {};
        // ---
        async function get_categories() {
            const response = await fetch('/api.php?get=categories');
            const data = await response.json();

            data.results.forEach(item => {
                campaign_to_categories[item.campaign] = item.category;
            });
        }
        // ---
        function getFormData(d) {
            // d['get'] = 'top_users';
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
            d["cat"] = campaign_to_categories[d["camp"]] ?? '';
            // ---
            return d;
        }
        // ---
        await get_categories();
        // ---
        $('#Topusers').DataTable({
            stateSave: true,
            paging: false,
            info: false,
            searching: false,
            ajax: {
                url: '/api.php?get=top_users',
                data: getFormData,
                dataSrc: function(json) {
                    // احتساب الإجماليات
                    let totalUsers = json.results.length;
                    let totalTargets = 0;
                    let totalWords = 0;
                    let totalViews = 0;

                    json.results.forEach(function(row) {
                        totalTargets += Number(row.targets) || 0;
                        totalWords += Number(row.words) || 0;
                        totalViews += Number(row.views) || 0;
                    });

                    // تحديث عناصر HTML
                    $('#c_user').text(totalUsers.toLocaleString());
                    $('#c_articles').text(totalTargets.toLocaleString());
                    $('#c_words').text(totalWords.toLocaleString());
                    $('#c_pv').text(totalViews.toLocaleString());

                    return json.results;
                }
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
                        return `<a href="/Translation_Dashboard/leaderboard.php?get=users&user=${data}">${data}</a>`;
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
        graph_js_params('chart09', getFormData({}))
        // ---
        $('#Toplangs').DataTable({
            stateSave: true,
            paging: false,
            info: false,
            searching: false,
            ajax: {
                url: '/api.php?get=top_langs',
                data: getFormData,
                dataSrc: function(json) {
                    let total = json.results.length;
                    $('#c_lang').text(total);

                    return json.results;
                }
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
                    data: 'lang',
                    title: 'Language',
                    render: function(data, type, row, meta) {
                        return `<a href="/Translation_Dashboard/leaderboard.php?get=langs&langcode=${data}">${row.lang_name}</a>`;
                    }
                },
                {
                    data: 'targets',
                    title: 'Count',
                    render: function(data) {
                        return Number(data).toLocaleString();
                    }
                },
                // { data: 'words', visible: false },
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
    })
</script>
</body>

</html>
