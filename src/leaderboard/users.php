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

// users_html($mainlang, $mainuser, $year_y, $camp)
function users_html($mainlang, $year_y, $camp, $user_to_curl, $user_to_html)
{
    $output = '';
    //---
    $mainlang = rawurldecode(str_replace('_', ' ', $mainlang));
    //---
    // '[{"user":"Mr. Ibrahem","lang":"ar","cnt":14}]'
    $user_most_langs = get_td_or_sql_top_lang_of_users([$user_to_curl]);
    //---
    $user_langs = $user_most_langs[0]['lang'] ?? "";
    //---
    $u_tables = get_users_tables($user_to_curl, $year_y, $mainlang);
    //---
    $dd = $u_tables['dd'];
    $dd_Pending = $u_tables['dd_Pending'];
    $table_of_views = $u_tables['table_of_views'];
    //---
    $count_new = count($dd);
    //---
    $user_is_global_username = ($GLOBALS['global_username'] === $user_to_curl) ? true : false;
    //---
    [$table1, $main_table] = make_users_lead($dd, 'translations', $table_of_views, $user_is_global_username);
    //---
    $user_link = ($user_langs) ? make_target_url("User:$user_to_curl", $user_langs, $user_to_html) : make_mdwiki_user_url($user_to_html);
    //---
    $filter_data = ["user" => $user_to_curl, "lang" => $mainlang, "year" => $year_y, "camp" => $camp];
    //---
    $xtools = <<<HTML
        <!-- <div class="d-flex align-items-center justify-content-between"> -->
            <a href='https://xtools.wmflabs.org/globalcontribs/$user_to_html' target='_blank'>
                <!-- <span class='h4'>(XTools)</span> -->
                <img src='https://xtools.wmcloud.org/build/images/logo.svg' title='Xtools' width='80px'/>
            </a>
        <!-- </div> -->
    HTML;
    //---
    $user_div = <<<HTML
        <span class='h4 text-center'>
            User: $user_link
            <br>
            $xtools
        </span>
    HTML;
    //---
    $graph = graph_data_new($dd);
    //---
    $output .= lead_row($table1, $graph, $user_div, $filter_data, "user");
    //---
    $output .= <<<HTML
        <div class='card mt-1'>
            <div class='card-body p-1'>
                $main_table
            </div>
        </div>
    HTML;
    //---
    [$_, $table_pnd] = make_users_lead($dd_Pending, 'pending', $table_of_views, $user_is_global_username);
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
