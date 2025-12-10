<?PHP

namespace Leaderboard\Filter;

/*
Usage:
use function Leaderboard\Filter\leaderboard_filter;
use function Leaderboard\Filter\input_group;
*/

if (isset($_REQUEST['test']) || isset($_COOKIE['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};

use Tables\SqlTables\TablesSql;
use function TD\Render\Html\makeDropdown;
use function SQLorAPI\Funcs\get_pages_with_pupdate;
use function SQLorAPI\TopData\get_td_or_sql_status;
use function SQLorAPI\GetDataTab\get_td_or_sql_projects;
use function SQLorAPI\GetDataTab\get_td_or_sql_categories;

function input_group($title, $rows): string
{
    $d33 = <<<HTML
        <div class="input-group">
            <span class="input-group-text">%s</span>
            %s
        </div>
    HTML;
    //---
    return sprintf($d33, $title, $rows);
}

function make_camp_dropdown($camp): string
{
    //---
    $categories_tab = get_td_or_sql_categories();
    $categories_tab = array_column(array: $categories_tab, column_key: 'campaign');
    //---
    $y1 = makeDropdown(tab: $categories_tab, cat: $camp, id: 'camp', add: 'all');
    // ---
    $campDropdown = input_group(title: 'Campaign', rows: $y1);
    // ---
    return $campDropdown;
}

function make_project_dropdown($user_group): string
{
    //---
    $projects_tab = get_td_or_sql_projects();
    //---
    $user_groups = array_column(array: $projects_tab, column_key: 'g_title');
    //---
    // '["Benevity","Hearing","McMaster","OLI","ProZ","Shani","TWB","TWB\\/WikiMed (Arabic)","Uncategorized","Wiki"]'
    // var_export(json_encode($user_groups));
    //---
    $y2 = makeDropdown(tab: $user_groups, cat: $user_group, id: 'user_group', add: 'all');
    // ---
    $projectDropdown = input_group(title: 'Translators', rows: $y2);
    // ---
    return $projectDropdown;
}

function make_year_dropdown($year): string
{
    //---
    $m_years2 = get_pages_with_pupdate();
    //---
    // sort $m_years2 from biggest to smallest
    rsort(array: $m_years2);
    //---
    $y3 = makeDropdown(tab: $m_years2, cat: $year, id: 'year', add: 'all');
    $yearDropdown = input_group(title: 'Year', rows: $y3);
    // ---
    return $yearDropdown;
}

function make_month_dropdown($month, $graph_data): string
{
    //---
    // array ( '2024-01' => 92, '2024-02' => 222, '2024-03' => 231, '2024-04' => 160, '2024-05' => 214, '2024-06' => 146, '2024-07' => 145, '2024-08' => 73, '2024-09' => 503, '2024-10' => 359, '2024-11' => 207, '2024-12' => 204, )
    //---
    // 2024-01 > 01
    //---
    $months_list = array_unique(array: array_map(
        callback: fn($item): int|string => date(
            format: 'm',
            timestamp: strtotime(datetime: $item)
        ),
        array: array_keys(array: $graph_data)
    ));
    //---
    // sort $m_months from biggest to smallest
    rsort(array: $months_list);
    //---
    $y3 = makeDropdown(tab: $months_list, cat: $month, id: 'month', add: 'All');
    // ---
    // $monthDropdown = input_group('Month', $y3);
    $monthDropdown = <<<HTML
        <div class="input-group w-50">
            $y3
        </div>
    HTML;
    // ---
    return $monthDropdown;
}
function leaderboard_filter($year, $month, $user_group, $camp, $action = "leaderboard.php"): string
{
    //---
    $campDropdown = make_camp_dropdown(camp: $camp);
    //---
    $projectDropdown = make_project_dropdown(user_group: $user_group);
    //---
    $yearDropdown = make_year_dropdown(year: $year);
    //---
    $cat = TablesSql::$s_camp_to_cat[$camp] ?? '';
    //---
    $monthDropdown = "";
    //---
    if ($year !== 'all') {
        $graph_data = get_td_or_sql_status(_year: $year, _user_group: $user_group, _cat: $cat);
        //---
        $monthDropdown = make_month_dropdown(month: $month, graph_data: $graph_data);
    };
    //---
    $test_line = (isset($_REQUEST['test']) != '') ? '<input type="hidden" name="test" value="1" />' : "";
    $test_line .= (isset($_GET['use_td_api']) != '') ? "<input type='hidden' name='use_td_api' value='" . htmlspecialchars(string: $_GET['use_td_api'], flags: ENT_QUOTES, encoding: 'UTF-8') . "'/>" : "";
    //---
    return <<<HTML
        <form method="get" action="$action" id="leaderboard_filter">
            <div class="row g-3">
                <div class="col-md-3">
                    <span align="center">
                        <h3>Leaderboard</h3>
                    </span>
                </div>
                <div class="col-md-7">
                    <div class="row">
                        <div class="col-md-4">
                            $campDropdown
                        </div>
                        <div class="col-md-4">
                            $projectDropdown
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex justify-content-center align-items-center gap-2">
                                $yearDropdown
                                $monthDropdown
                            </div>
                        </div>
                    </div>
                </div>
                <div class="aligncenter col-md-1 col-sm-3">
                    $test_line
                    <input class='btn btn-outline-primary' type='submit' value='Filter' />
                </div>
            </div>
        </form>
    HTML;
}
