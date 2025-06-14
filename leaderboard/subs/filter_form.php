<?PHP

namespace Leaderboard\Subs\FilterForm;
/*
Usage:

use function Leaderboard\Subs\FilterForm\make_filter_form_langs;
use function Leaderboard\Subs\FilterForm\make_filter_form_users;
use function Leaderboard\Subs\FilterForm\lead_row;

*/

//---
use function Actions\Html\makeDropdown;

use function SQLorAPI\Funcs\get_lang_years;
use function SQLorAPI\Funcs\get_user_years;
use function SQLorAPI\Funcs\get_user_langs;

$test_line = (isset($_REQUEST['test']) != '') ? "<input type='text' name='test' value='1' hidden/>" : "";



function form_result($hidden, $Dropdowns)
{
    global $test_line;
    //---
    return <<<HTML
        <form method="get" action="leaderboard.php">
            $test_line
            $hidden
            <div class='container g-0 mt-3'>
                <div class='row'>
                    <div class="col-md-8">
                        $Dropdowns
                    </div>
                    <div class="col-md-4">
                        <input class='btn btn-outline-primary' type='submit' value='Filter' />
                    </div>
                </div>
            </div>
        </form>
    HTML;
}
function make_filter_form_langs($mainlang, $year_y)
{
    $d33 = <<<HTML
        <div class="input-group">
            <span class="input-group-text">%s</span>
            %s
        </div>
    HTML;

    $years = get_lang_years($mainlang);
    //---
    $y3 = makeDropdown($years, $year_y, 'year', 'All');
    //---
    $yearDropdown = sprintf($d33, 'Year', $y3);
    //---
    return form_result("<input type='hidden' name='langcode' value='$mainlang' />", $yearDropdown);
}


function make_filter_form_users($user, $lang_y, $year_y)
{
    $d33 = <<<HTML
        <div class="input-group">
            <span class="input-group-text">%s</span>
            %s
        </div>
    HTML;
    //---
    $years = get_user_years($user);
    $langs = get_user_langs($user);
    //---
    $y2 = makeDropdown($langs, $lang_y, 'lang', 'All');
    //---
    $langsDropdown = sprintf($d33, 'Lang', $y2);
    //---
    $y3 = makeDropdown($years, $year_y, 'year', 'All');
    //---
    $yearDropdown = sprintf($d33, 'Year', $y3);
    //---
    $Dropdown = $langsDropdown . $yearDropdown;
    //---
    return form_result("<input type='hidden' name='user' value='$user' />", $Dropdown);
    //---
}

function lead_row($table1, $graph, $main_title, $filter_form)
{
    //---
    // $table1 = ['total_articles' => $total_articles, 'total_words' => $total_words, 'total_views' => $total_views];
    $total_articles = number_format($table1['total_articles']);
    $total_words = number_format($table1['total_words']);
    $total_views = number_format($table1['total_views']);
    //---
    $table1_html = <<<HTML
        <table class='table table-sm table-striped'>
            <tr><td>Articles: </td><td>$total_articles</td>
            <td>Words: </td><td>$total_words</td>
            <td>Pageviews: </td><td><span id='hrefjsontoadd'>$total_views</span></td></tr>
        </table>
        HTML;
    //---
    return <<<HTML
        <div class='container-fluid'>
            <div class='row lead_forms'>
                <div class='col-lg-4 col-md-6 border_debug border rounded'>
                    <div class="d-flex align-items-center justify-content-center " style="height: 100%">
                        <div class="list-group">
                            <div>
                            $main_title
                            </div>
                            <div>
                            $table1_html
                            </div>
                        </div>
                    </div>
                </div>
                <div class='col-lg-5 col-md-6'>
                    <div class="position-relative py-1 border rounded">
                        $graph
                    </div>
                </div>
                <div class='col-lg-3 col-md-6 border_debug'>
                    $filter_form
                </div>
            </div>
        </div>
    HTML;
}
