<?PHP

namespace Leaderboard\Subs\LeadHelp;

/*
Usage:

use function Leaderboard\Subs\LeadHelp\make_key;
use function Leaderboard\Subs\LeadHelp\make_td_fo_user;
use function Leaderboard\Subs\LeadHelp\make_table_lead;
use function Leaderboard\Subs\LeadHelp\make_langs_lead;
use function Leaderboard\Subs\LeadHelp\make_users_lead;

*/

use Tables\Main\MainTables;
use Tables\SqlTables\TablesSql;
use function APICalls\WikiApi\make_view_by_number;
use function TD\Render\Html\make_mdwiki_cat_url;
use function TD\Render\Html\make_mdwiki_article_url;
use function TD\Render\Html\make_target_url;
use function Results\TrLink\make_translate_link_medwiki;
use function Leaderboard\Camps\get_articles_to_camps;
// use function TD\Render\Html\make_translation_url;

function make_key($Taab)
{
    $dat = '';
    //---
    foreach (['pupdate', 'date', 'add_date'] as $key) {
        if (!empty($Taab[$key])) {
            $dat = $Taab[$key];
            break;
        }
    }
    //---
    // if $_date_ has : then split before first space
    if (strpos($dat, ':') !== false) {
        $dat = explode(' ', $dat)[0];
    };
    //---
    $urt = '';
    //---
    if (!empty($dat)) {
        $urt = str_replace('-', '', $dat) . ':';
    };
    //---
    $kry = $urt . $Taab['lang'] . ':' . $Taab['title'];
    //---
    return $kry;
}

function make_td_fo_user($tabb, $number, $view_number, $word, $page_type, $tab_ty, $user_is_global_username)
{
    //---
    // $page_type = 'users' or 'langs' only
    if ($page_type != 'users' && $page_type != 'langs') {
        $page_type = 'users';
    };
    //---
    $catto_camp_new = TablesSql::$s_cat_to_camp;
    $articlesto_camps = get_articles_to_camps();
    //---
    $mdtitle = trim($tabb['title']);
    $user    = $tabb['user'] ?? "";
    $lang    = $tabb['lang'] ?? "";
    $cat     = $tabb['cat'] ?? "";
    $deleted = $tabb['deleted'] ?? "";
    $pupdate = $tabb['pupdate'] ?? "";
    //---
    $date    = $tabb['date'] ?? $tabb['add_date'] ?? "";
    //---
    // if $_date_ has : then split before first space
    if (strpos($date, ':') !== false) {
        $date = explode(' ', $date)[0];
    };
    //---
    $word = number_format($word);
    //---
    $mdwiki_url = make_mdwiki_article_url($mdtitle);
    //---
    $cat_or_camp_link = make_mdwiki_cat_url($cat);
    //---
    $new_camps = $articlesto_camps[$mdtitle] ?? [];
    //---
    $campaign = $catto_camp_new[$cat] ?? '';
    $campaign_data = $campaign;
    //---
    // 2023-08-22
    if (count($new_camps) > 0) {
        $campaign_data = "";
        $cat_or_camp_link = "";
        foreach ($new_camps as $camp) {
            $cat_or_camp_link .= "<a href='leaderboard.php?camp=$camp' style='white-space: nowrap;'>$camp</a><br>";
            $campaign_data .= "$camp, ";
        }
        // remove last <br>
        $cat_or_camp_link = substr($cat_or_camp_link, 0, -4);
    } else {
        // echo "No campaigns for $mdtitle<br>";
        if (!empty($campaign)) {
            $cat_or_camp_link = "<a href='leaderboard.php?camp=$campaign'>$campaign</a>";
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
        $urll = "<a href='leaderboard.php?get=langs&langcode=$lang'><span style='white-space: nowrap;'>$lang</span></a>";
        $urll_data = $lang;
    } else {
        $use = rawurlEncode($user);
        $use = str_replace('+', '_', $use);
        //---
        $urll = "<a href='leaderboard.php?get=users&user=$use'><span style='white-space: nowrap;'>$user</span></a>";
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
        $complete   = ($user_is_global_username) ? "<td data-content='complete'><a target='_blank' href='$tralink'>complete</a></td>" : '';
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
                $mdwiki_url
            </td>
            <td data-content="Campaign" data-filter="$campaign_data">
                $cat_or_camp_link
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

function make_table_lead($dd, $tab_type, $views_table, $page_type, $user_is_global_username)
{
    $total_words = 0;
    $total_views = 0;
    //---
    // if $views_table is not array
    if (!is_array($views_table)) {
        $views_table = [];
    }
    //---
    $user_or_lang = ($page_type == 'users') ? 'Lang.' : 'User';
    //---
    $tab_views  = ($tab_type == 'pending') ? '' : '<th>Views</th>';
    $th_Date    = ($tab_type == 'pending') ? 'Start date' : 'Date';
    $complete   = ($tab_type == 'pending' && $user_is_global_username) ? '<th>complete!</th>' : '';
    //---
    $leadtable = ($tab_type == 'pending') ? 'leadtable2' : 'leadtable';
    //---
    // table-mobile-responsive
    $table2 = <<<HTML
        <table class='table table-striped compact table_text_left table_responsive' id='$leadtable'>
            <thead>
                <tr>
                    <th>#</th>
                    <th data-priority="1">$user_or_lang</th>
                    <th data-priority="2">Title</th>
                    <th>Campaign</th>
                    <th>Words</th>
                    <th data-priority="3">Translated</th>
                    <th>$th_Date</th>
                    $tab_views
                    $complete
                </tr>
            </thead>
            <tbody>
        HTML;
    //---
    $total_articles = count($dd);
    $noo = 0;
    //---
    foreach ($dd as $tat => $tabe) {
        //---
        $noo += 1;
        //---
        $deleted = $tabe['deleted'] ?? 0;
        //---
        $target  = $tabe['target'] ?? "";
        $lange    = $tabe['lang'] ?? "";
        //---
        $view_number  = $tabe['views'] ?? 0;
        // ---
        if ($view_number == 0) $view_number = $views_table[$lange][$target] ?? 0;
        //---
        if ($deleted == 1) {
            $view_number = 0;
        }
        //---
        $total_views += $view_number;
        //---
        $mdtitle = $tabe['title'] ?? "";
        $word2 = MainTables::$x_Words_table[$mdtitle] ?? 0;
        $word = $tabe['word'] ?? 0;
        //---
        if ($word < 1) $word = $word2;
        //---
        $total_words += $word;
        //---
        $table2 .= make_td_fo_user($tabe, $noo, $view_number, $word, $page_type, $tab_type, $user_is_global_username);
    };
    //---
    $table2 .= <<<HTML
        </tbody>
        <tfoot>
        </tfoot>
    </table>
    HTML;
    //---
    $table1 = ['total_articles' => $total_articles, 'total_words' => $total_words, 'total_views' => $total_views];
    //---
    return [$table1, $table2];
}

function make_users_lead($tab, $tab_type, $views_table, $user_is_global_username)
{
    //---
    [$_, $table_pnd] = make_table_lead(
        $tab,
        $tab_type,
        $views_table,
        'users',
        $user_is_global_username
    );
    // ---
    return [$_, $table_pnd];
}

function make_langs_lead($tab, $tab_type, $views_table, $lang)
{
    [$_, $table_pnd] = make_table_lead(
        $tab,
        $tab_type,
        $views_table,
        'langs',
        false
    );
    // ---
    return [$_, $table_pnd];
}
