<?PHP

namespace Leaderboard\Subs\FilterForm;
/*
Usage:

use function Leaderboard\Subs\FilterForm\lead_row;

*/

//---
use function SQLorAPI\Funcs\get_lang_years;
use function SQLorAPI\Funcs\get_user_years;
use function SQLorAPI\Funcs\get_user_langs;

function DropdownNew($title, $tab, $cat, $id)
{
    //---
    $options = "";
    //---
    foreach ($tab as $dd) {
        $se = ($cat == $dd) ? 'selected' : '';
        //---
        $options .= <<<HTML
            <option value='$dd' $se>$dd</option>
        HTML;
    };
    //---
    return <<<HTML
        <select dir="ltr" id="$id" name="$id" class="form-select" data-bs-theme="auto">
            <option value='all'>$title: All</option>
            $options
        </select>
    HTML;
}
function make_filter_html($data, $filter_page)
{
    //---
    // $filter_data = ["user" => "", "lang" => $mainlang, "year" => $year_y];
    //---
    $lang     = $data['lang'];
    $year     = $data['year'];
    $user     = $data['user'];
    //---
    if ($filter_page == 'user') {
        $years = get_user_years($user);
        $langs = get_user_langs($user);
        //---
        $langsDropdown = DropdownNew('Lang', $langs, $lang, 'lang');
        $yearDropdown  = DropdownNew('Year', $years, $year, 'year');
        //---
        $Dropdown = <<<HTML
            <div class="col-6">
                $langsDropdown
            </div>
            <div class="col-6">
                $yearDropdown
            </div>
        HTML;
        //---
        $hidden = "<input type='hidden' name='user' value='$user' />";
        //---
    } else {
        $years = get_lang_years($lang);
        //---
        $yearDropdown = DropdownNew('Year', $years, $year, 'year');
        //---
        $Dropdown = <<<HTML
            <div class="col-12">
                $yearDropdown
            </div>
        HTML;
        //---
        $hidden = "<input type='hidden' name='langcode' value='$lang' />";
    }
    //---
    $test_line = (isset($_REQUEST['test']) != '') ? '<input type="hidden" name="test" value="1" />' : "";
    //---
    return <<<HTML
        <form method="get" action="leaderboard.php" class="border rounded">
            $test_line
            $hidden
            <div class='container mt-3'>
                <div class='row g-1'>
                    $Dropdown
                    <div class="col-12 mt-1">
                        <button type="submit" class="btn btn-sm btn-outline-primary w-100">Filter</button>
                    </div>
                </div>
            </div>
        </form>
    HTML;
}

function make_table1_html($table1)
{
    //---
    // $table1 = ['total_articles' => $total_articles, 'total_words' => $total_words, 'total_views' => $total_views];
    $total_articles = number_format($table1['total_articles']);
    $total_words = number_format($table1['total_words']);
    $total_views = number_format($table1['total_views']);
    //---
    $table1_html = <<<HTML
        <div class="text-muted">
            Articles: <strong>$total_articles</strong> &nbsp;
            Words: <strong>$total_words</strong> &nbsp;
            Pageviews: <strong><span id="hrefjsontoadd">$total_views</span></strong>
        </div>
        HTML;
    //---
    return $table1_html;
}

function lead_row($table1, $graph, $main_title, $filter_data, $filter_page)
{
    //---
    $table1_html = make_table1_html($table1);
    //---
    $filter_form = make_filter_html($filter_data, $filter_page);
    //---
    return <<<HTML
        <div class='container-fluid'>
            <div class='row g-1'>
                <div class='col-lg-4 col-md-12 border_debug border rounded'>
                    <div class="d-flex align-items-center justify-content-center" style="height: 100%">
                        <div class="list-group">
                            $main_title
                            <div class="d-flex align-items-center justify-content-center " style="height: 100%">
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
