<?PHP

namespace Leaderboard\Subs\FilterForm;
/*
Usage:

use function Leaderboard\Subs\FilterForm\make_filter_form_langs;
use function Leaderboard\Subs\FilterForm\make_filter_form_users;

*/

//---
use function Actions\Html\makeDropdown;

use function SQLorAPI\Funcs\get_lang_years;
use function SQLorAPI\Funcs\get_user_years;
use function SQLorAPI\Funcs\get_user_langs;

$test_line = (isset($_REQUEST['test']) != '') ? "<input type='text' name='test' value='1' hidden/>" : "";

function make_filter_form_langs($mainlang, $year_y)
{
    global $test_line;
    //---
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
    return <<<HTML
        <form method="get" action="leaderboard.php">
            <input type="hidden" name="langcode" value="$mainlang" />
            <div class='container g-3'>
                <div class='row content'>
                    <div class="col-md-4">
                        $yearDropdown
                    </div>
                    <div class="aligncenter col-md-6">
                        $test_line
                        <input class='btn btn-outline-primary' type='submit' name='start' value='Filter' />
                    </div>
                </div>
            </div>
        </form>
        HTML;
}


function make_filter_form_users($user, $lang_y, $year_y)
{
    global $test_line;
    //---
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
    return <<<HTML
        <form method="get" action="leaderboard.php">
            <input type="hidden" name="user" value="$user" />
            <div class='container g-3'>
                <div class='row content'>
                    <div class="col-md-4">
                        $langsDropdown
                    </div>
                    <div class="col-md-4">
                        $yearDropdown
                    </div>
                    <div class="aligncenter col-md-4">
                        $test_line
                        <input class='btn btn-outline-primary' type='submit' name='start' value='Filter' />
                    </div>
                </div>
            </div>
        </form>
        HTML;
}
//---
