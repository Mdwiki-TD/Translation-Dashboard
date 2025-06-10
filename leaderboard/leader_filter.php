<?PHP

namespace Leaderboard\Filter;

/*
Usage:
use function Leaderboard\Filter\leaderboard_filter;
*/

if (isset($_REQUEST['test']) || isset($_COOKIE['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};

use function Actions\Html\makeDropdown;
use function SQLorAPI\Funcs\get_pages_with_pupdate;
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
    $categories_tab = array_column($categories_tab, 'campaign');
    //---
    $y1 = makeDropdown($categories_tab, $camp, 'camp', 'all');
    // ---
    $campDropdown = input_group('Campaign', $y1);
    // ---
    return $campDropdown;
}

function make_project_dropdown($user_group): string
{
    //---
    $projects_tab = get_td_or_sql_projects();
    //---
    $user_groups = array_column($projects_tab, 'g_title');
    //---
    // '["Benevity","Hearing","McMaster","OLI","ProZ","Shani","TWB","TWB\\/WikiMed (Arabic)","Uncategorized","Wiki"]'
    // var_export(json_encode($user_groups));
    //---
    $y2 = makeDropdown($user_groups, $user_group, 'user_group', 'all');
    // ---
    $projectDropdown = input_group('Translators', $y2);
    // ---
    return $projectDropdown;
}

function make_year_dropdown($year): string
{
    //---
    $m_years2 = get_pages_with_pupdate();
    //---
    // sort $m_years2 from biggest to smallest
    rsort($m_years2);
    //---
    $y3 = makeDropdown($m_years2, $year, 'year', 'all');
    $yearDropdown = input_group('Year', $y3);
    // ---
    return $yearDropdown;
}

function leaderboard_filter($year, $user_group, $camp, $action = "leaderboard.php"): string
{
    //---
    $campDropdown = make_camp_dropdown($camp);
    //---
    $projectDropdown = make_project_dropdown($user_group);
    //---
    $yearDropdown = make_year_dropdown($year);
    //---
    $test_line = (isset($_REQUEST['test']) != '') ? "<input type='text' name='test' value='1' hidden/>" : "";
    $test_line .= (isset($_GET['use_td_api']) != '') ? "<input type='text' name='use_td_api' value='" . $_GET['use_td_api'] . "' hidden/>" : "";
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
                        <div class="col-md-5">
                            $campDropdown
                        </div>
                        <div class="col-md-4">
                            $projectDropdown
                        </div>
                        <div class="col-md-3">
                            $yearDropdown
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
