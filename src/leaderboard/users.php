<?PHP

namespace Leaderboard\Users;

/*
Usage:

use function Leaderboard\Users\users_html;

*/

use function Leaderboard\Subs\LeadHelp\make_users_lead;
use function TD\Render\Html\make_mdwiki_user_url;
use function TD\Render\Html\make_target_url;
use function Leaderboard\Subs\SubUsers\get_users_tables;
use function Leaderboard\SubGraph\graph_data_new;
use function Leaderboard\Subs\FilterForm\lead_row;
use function SQLorAPI\TopData\get_td_or_sql_top_lang_of_users;

function users_html($mainlang, $mainuser, $year_y, $camp)
{
    $output = '';
    //---
    $mainlang = rawurldecode(str_replace('_', ' ', $mainlang));
    //---
    // '[{"user":"Mr. Ibrahem","lang":"ar","cnt":14}]'
    $user_langs = get_td_or_sql_top_lang_of_users([$mainuser]);
    //---
    $user_most_lang = $user_langs[0]['lang'];
    //---
    $u_tables = get_users_tables($mainuser, $year_y, $mainlang);
    //---
    $dd = $u_tables['dd'];
    $dd_Pending = $u_tables['dd_Pending'];
    $table_of_views = $u_tables['table_of_views'];
    //---
    $count_new = count($dd);
    //---
    [$table1, $main_table] = make_users_lead($dd, 'translations', $table_of_views, $mainuser);
    //---
    $user_link = ($user_most_lang) ? make_target_url("User:$mainuser", $user_most_lang, $mainuser) : make_mdwiki_user_url($mainuser);
    //---
    $graph = graph_data_new($dd);
    //---
    $filter_data = ["user" => $mainuser, "lang" => $mainlang, "year" => $year_y, "camp" => $camp];
    //---
    $xtools = <<<HTML
            <div class="d-flex align-items-center justify-content-between">
                <span class='h4'>User: $user_link </span>
                <a href='https://xtools.wmflabs.org/globalcontribs/$mainuser' target='_blank'>
                <span class='h4'>(XTools)</span>
                <!-- <img src='https://xtools.wmcloud.org/build/images/logo.svg' title='Xtools' width='80px'/> -->
            </a>
            </div>
    HTML;
    //---
    $output .= lead_row($table1, $graph, $xtools, $filter_data, "user");
    //---
    $output .= <<<HTML
        <div class='card mt-1'>
            <div class='card-body p-1'>
                $main_table
            </div>
        </div>
    HTML;
    //---
    [$_, $table_pnd] = make_users_lead($dd_Pending, 'pending', $table_of_views, $mainuser);
    //---
    $output .= <<<HTML
        <br>
        <div class='card'>
            <div class='card-body' style='padding:5px 0px 5px 5px;'>
                <h2 class='text-center'>Translations in process</h2>
                $table_pnd
            </div>
        </div>
    HTML;
    // ---
    return $output;
}
