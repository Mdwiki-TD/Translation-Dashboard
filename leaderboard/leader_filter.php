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

use Tables\SqlTables\TablesSql;
use function Actions\Html\makeDropdown;
use function SQLorAPI\Funcs\get_pages_with_pupdate;
use function SQLorAPI\GetDataTab\get_td_or_sql_projects;

function leaderboard_filter($year, $user_group, $camp, $action = "leaderboard.php"): string
{
    //---
    $d33 = <<<HTML
        <div class="input-group">
            <span class="input-group-text">%s</span>
            %s
        </div>
        HTML;
    //---
    $y1 = makeDropdown(TablesSql::$s_cat_titles, $camp, 'camp', 'all');
    $campDropdown = sprintf($d33, 'Campaign', $y1);
    //---
    $projects_tab = get_td_or_sql_projects();
    //---
    $user_groups = array_column($projects_tab, 'g_title');
    //---
    // '["Benevity","Hearing","McMaster","OLI","ProZ","Shani","TWB","TWB\\/WikiMed (Arabic)","Uncategorized","Wiki"]'
    // var_export(json_encode($user_groups));
    //---
    $y2 = makeDropdown($user_groups, $user_group, 'user_group', 'all');
    $projectDropdown = sprintf($d33, 'Translators', $y2);
    //---
    $m_years2 = get_pages_with_pupdate();
    //---
    // sort $m_years2 from biggest to smallest
    rsort($m_years2);
    //---
    $y3 = makeDropdown($m_years2, $year, 'year', 'all');
    $yearDropdown = sprintf($d33, 'Year', $y3);
    //---
    $test_line = (isset($_REQUEST['test']) != '') ? "<input type='text' name='test' value='1' hidden/>" : "";
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
