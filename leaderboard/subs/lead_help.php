<?PHP

namespace Leaderboard\LeadHelp;

/*
Usage:

use function Leaderboard\LeadHelp\make_td_fo_user;
use function Leaderboard\LeadHelp\make_table_lead;

*/

//---
include_once __DIR__ . '/../camps.php';

use function Actions\WikiApi\make_view_by_number;
use function Actions\Html\make_cat_url;
use function Actions\Html\make_mdwiki_title;
use function Actions\Html\make_translation_url;
use function Actions\Html\make_target_url;
use function Actions\Html\make_translate_link_medwiki;

function make_td_fo_user($tabb, $number, $view_number, $word, $page_type = 'users', $tab_ty = 'a', $_user_ = '')
{
    global $cat_to_camp, $articles_to_camps, $camps_to_articles;
    //---
    $mdtitle = trim($tabb['title']);
    $user    = $tabb['user'] ?? "";
    $date    = $tabb['date'] ?? "";
    $lang    = $tabb['lang'] ?? "";
    $cat     = $tabb['cat'] ?? "";
    $deleted = $tabb['deleted'] ?? "";
    $pupdate = $tabb['pupdate'] ?? "";
    //---
    $word = number_format($word);
    //---
    $nana = make_mdwiki_title($mdtitle);
    //---
    $ccat = make_cat_url($cat);
    //---
    $new_camps = $articles_to_camps[$mdtitle] ?? [];
    //---
    $campaign = $cat_to_camp[$cat] ?? '';
    $campaign_data = $campaign;
    //---
    // 2023-08-22
    if (count($new_camps) > 0) {
        $campaign_data = "";
        $ccat = "";
        foreach ($new_camps as $camp) {
            $ccat .= "<a href='leaderboard.php?camp=$camp' style='white-space: nowrap;'>$camp</a><br>";
            $campaign_data .= "$camp, ";
        }
        // remove last <br>
        $ccat = substr($ccat, 0, -4);
    } else {
        // echo "No campaigns for $mdtitle<br>";
        if (!empty($campaign)) {
            $ccat = "<a href='leaderboard.php?camp=$campaign'>$campaign</a>";
        };
    };
    //---
    $tran_type = $tabb['translate_type'] ?? '';
    //---
    $usr_or_lang = ($page_type == 'users') ? "Lang" : "User";
    //---
    $urll_data = '';
    //---
    if ($page_type == 'users') {
        $urll = "<a href='leaderboard.php?langcode=$lang'><span style='white-space: nowrap;'>$lang</span></a>";
        $urll_data = $lang;
    } else {
        $use = rawurlEncode($user);
        $use = str_replace('+', '_', $use);
        //---
        $urll = "<a href='leaderboard.php?user=$use'><span style='white-space: nowrap;'>$user</span></a>";
        $urll_data = $user;
        //---
    };
    //---
    $udate = $pupdate;
    $complete   = '';
    //---
    $target = "";
    //---
    if ($tab_ty == 'pending') {
        $udate = $date;
        $target_link = 'Pending';
        $td_views = '';
        //---
        // $tralink = make_translation_url($mdtitle, $lang, $tran_type);
        $tralink = make_translate_link_medwiki($mdtitle, $lang, $cat, "", $tran_type);
        $complete   = ($GLOBALS['global_username'] === $_user_) ? "<td data-content='complete'><a target='_blank' href='$tralink'>complete</a></td>" : '';
    } else {
        $target  = trim($tabb['target']);
        //---
        $view = "-";
        if ($deleted == 0) {
            $view = make_view_by_number($target, $view_number, $lang, $pupdate);
        }
        //---
        $target_link = make_target_url($target, $lang, $name = "", $deleted = $deleted);
        //---
        $td_views = "<td data-content='Views' data-sort='$view_number' data-filter='$view_number'>$view</td>";
    };
    //---
    $year = substr($udate, 0, 4);
    //---
    $laly = <<<HTML
        <!-- <tr class='filterDiv show2 $year'> -->
        <tr>
            <th data-content="#">
                $number
            </th>
            <td data-content="$usr_or_lang" data-filter="$urll_data">
                $urll
            </td>
            <td data-content="Title" data-filter="$mdtitle">
                $nana
            </td>
            <td data-content="Campaign" data-filter="$campaign_data">
                $ccat
            </td>
            <td data-content="Words" data-filter="$word">
                $word
            </td>
            <td data-content="Translated" data-filter="$target">
                $target_link
            </td>
            <td data-content="Date" class='spannowrap' data-filter="$year">
                $udate
            </td>
            $td_views
            $complete
        </tr>
        HTML;
    //---
    return $laly;
    //---
};

function make_table_lead($dd, $tab_type = 'a', $views_table = array(), $page_type = 'users', $user = '', $lang = '')
{
    //---
    global $Words_table;
    //---
    $total_words = 0;
    $total_views = 0;
    //---
    $user_or_lang = ($page_type == 'users') ? 'Lang.' : 'User';
    //---
    $tab_views  = ($tab_type == 'pending') ? '' : '<th>Views</th>';
    $th_Date    = ($tab_type == 'pending') ? 'Start date' : 'Date';
    $complete   = ($tab_type == 'pending' && $GLOBALS['global_username'] === $user) ? '<th>complete!</th>' : '';
    //---
    $leadtable = ($tab_type == 'pending') ? 'leadtable2' : 'leadtable';
    //---

    //---
    $sato = <<<HTML
        <table class='table table-striped compact soro table-mobile-responsive table-mobile-sided' id='$leadtable'>
            <thead>
                <tr>
                    <th>#</th>
                    <th>$user_or_lang</th>
                    <th>Title</th>
                    <th>Campaign</th>
                    <th>Words</th>
                    <!-- <th>Type</th> -->
                    <th>Translated</th>
                    <th>$th_Date</th>
                    $tab_views
                    $complete
                </tr>
            </thead>
            <tbody>
        HTML;
    //---
    $noo = 0;
    foreach ($dd as $tat => $tabe) {
        //---
        $noo += 1;
        //---
        $deleted = $tabe['deleted'] ?? 0;
        //---
        $target  = $tabe['target'] ?? "";
        $lang    = $tabe['lang'] ?? "";
        //---
        $view_number  = $tabe['views'] ?? 0;
        // ---
        if ($view_number == 0) $view_number = $views_table[$lang][$target] ?? 0;
        //---
        if ($deleted == 1) {
            $view_number = 0;
        }
        //---
        $total_views += $view_number;
        //---
        $mdtitle = $tabe['title'] ?? "";
        $word2 = $Words_table[$mdtitle] ?? 0;
        $word = $tabe['word'] ?? 0;
        //---
        if ($word < 1) $word = $word2;
        //---
        $total_words += $word;
        //---
        $sato .= make_td_fo_user($tabe, $noo, $view_number, $word, $page_type = $page_type, $tab_ty = $tab_type, $_user_ = $user);
        //---
    };
    //---
    $sato .= <<<HTML
        </tbody>
        <tfoot>
        </tfoot>
    </table>
    HTML;
    //---
    $table1 = <<<HTML
            <table class='table table-sm table-striped' style='width:70%;'>
            <tr><td>Words: </td><td>$total_words</td></tr>
            <tr><td>Pageviews: </td><td><span id='hrefjsontoadd'>$total_views</span></td></tr>
            </table>
        HTML;
    //---
    $arra = array('table1' => $table1, 'table2' => $sato);
    //---
    return $arra;
    //---
};
