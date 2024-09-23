<?PHP

namespace Leaderboard\LeaderTabUsers;

/*
Usage:

use function Leaderboard\LeaderTabUsers\makeUsersTable;
use function Leaderboard\LeaderTabUsers\module_copy;

*/

// include_once __DIR__ . '/leader_tables_tabs.php';

use function Actions\Html\make_modal_fade;

function module_copy()
{

    global $sql_users_tab, $sql_users_tab_to_lang;
    arsort($sql_users_tab);

    $usrse = [];

    foreach ($sql_users_tab as $user => $usercount) {
        $usrse[$user] = isset($sql_users_tab_to_lang[$user]) ? $sql_users_tab_to_lang[$user] : [];
    };

    $lal = "<textarea cols='55' rows='10' id='users_targets' name='users_targets'>";

    foreach ($usrse as $user => $langs) {
        // sort $langs by numbers
        arsort($langs);
        // get first item in $langs
        $lan = array_keys($langs)[0];
        $lal .= "#{{#target:User:$user|$lan.wikipedia.org}}
    ";
    }
    //---
    $lal .= '</textarea>';
    $lal .= <<<HTML
        <script>
            function copy_targets() {
                let textarea = document.getElementById("users_targets");
                textarea.select();
                document.execCommand("copy");
            }
        </script>
    HTML;
    //---
    $modal = make_modal_fade('', $lal, 'targets', '<a class="btn btn-outline-primary" onclick="copy_targets()">Copy</a>');
    //---
    return $modal;
}

function makeUsersTable($min = 2)
{

    global $sql_users_tab, $Users_word_table, $Views_by_users;
    //---

    arsort($sql_users_tab);
    $numb = 0;

    $trs = "";
    foreach ($sql_users_tab as $user => $usercount) {
        // if ($usercount < $min && $numb > 15) continue;
        $numb += 1;
        $usercount = number_format($usercount);
        $views = isset($Views_by_users[$user]) ? number_format($Views_by_users[$user]) : 0;
        $words = isset($Users_word_table[$user]) ? number_format($Users_word_table[$user]) : 0;

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
