<?PHP

namespace Leaderboard\LeaderTabUsers;

/*
Usage:

use function Leaderboard\LeaderTabUsers\makeUsersTable;
use function Leaderboard\LeaderTabUsers\module_copy;

*/

// include_once __DIR__ . '/leader_tables_tabs.php';
use Leaderboard\Tabs\LeaderBoardTabs;
use function Actions\Html\make_modal_fade;
use function SQLorAPI\GetDataTab\get_td_or_sql_users_by_wiki;

function module_copy($year, $user_group, $cat)
{
    $users_tab = get_td_or_sql_users_by_wiki($year, $user_group, $cat);

    $lal = "<textarea cols='55' rows='10' id='users_targets' name='users_targets'>";

    foreach ($users_tab as $tab) {
        // get first item in $langs
        $user = $tab['user'];
        $lang = $tab['lang'];
        $lal .= "#{{#target:User:$user|$lang.wikipedia.org}}\n";
    }
    //---
    $lal .= '</textarea>';
    //---
    $modal = make_modal_fade('', $lal, 'targets', '<a class="btn btn-outline-primary" onclick="copy_target_text(\'users_targets\')">Copy</a>');
    //---
    return $modal;
}

function makeUsersTable($min = 2)
{
    //---
    $numb = 0;
    $trs = "";
    foreach (LeaderBoardTabs::$u_sql_users_tab as $user => $usercount) {
        // if ($usercount < $min && $numb > 15) continue;
        $numb += 1;
        $usercount = number_format($usercount);
        $views = isset(LeaderBoardTabs::$u_Views_by_users[$user]) ? number_format(LeaderBoardTabs::$u_Views_by_users[$user]) : 0;
        $words = isset(LeaderBoardTabs::$u_Users_word_table[$user]) ? number_format(LeaderBoardTabs::$u_Users_word_table[$user]) : 0;

        $use = rawurlEncode($user);
        $use = str_replace('+', '_', $use);

        $trs .= <<<HTML
            <tr>
                <td>$numb</td>
                <td><a href='leaderboard.php?user=$use'>$user</a></td>
                <td>$usercount</td>
                <td>$words</td>
                <td>$views</td>
            </tr>
            HTML;
    };

    $text = <<<HTML
        <table class='table compact table-striped sortable' style='margin-top: 0px !important;margin-bottom: 0px !important'>
            <thead>
                <tr>
                    <th class="spannowrap">#</th>
                    <th class="spannowrap">User</th>
                    <th>Number</th>
                    <th>Words</th>
                    <th>Pageviews</th>
                </tr>
            </thead>
            <tbody>
                $trs
            </tbody>
            <tfoot>
            </tfoot>
        </table>
    HTML;
    //---
    // $text .= module_copy();
    //---
    return $text;
}
