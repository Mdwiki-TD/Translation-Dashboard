<?PHP

namespace Leaderboard\LeaderTabUsers;

/*
Usage:

use function Leaderboard\LeaderTabUsers\makeUsersTable;
use function Leaderboard\LeaderTabUsers\module_copy_data;

*/

use function TD\Render\Html\make_modal_fade;

function module_copy_data($users_tab)
{
    $lal = "<textarea cols='55' rows='10' id='users_targets' name='users_targets'>";

    foreach ($users_tab as $tab) {
        // get first item in $langs
        $user = $tab['user'];
        $lang = $tab['lang'];
        // ---
        if (empty($lang) || empty($user)) continue;
        // ---
        $lal .= "#{{#target:User:$user|$lang.wikipedia.org}}\n";
    }
    //---
    $lal .= '</textarea>';
    //---
    $modal = make_modal_fade('', $lal, 'targets', '<a class="btn btn-outline-primary" onclick="copy_target_text(\'users_targets\')">Copy</a>');
    //---
    return $modal;
}

function makeUsersTable($users, $min = 2)
{
    //---
    // sort new_data by [lang][count]
    uasort($users, function ($a, $b) {
        return $b["count"] <=> $a["count"];
    });
    // ---
    $numb = 0;
    $trs = "";
    //---
    foreach ($users as $user => $tab) {
        // if ($usercount < $min && $numb > 15) continue;
        $numb += 1;
        // ---
        // type of $usercount // integer
        // ---
        $usercount = number_format($tab['count'] ?? 0);
        $views = number_format($tab['views'] ?? 0);
        // ---
        $words = $tab['words'] ?? 0;
        // ---
        // type of $words // string
        // echo gettype($words) . "<br>";
        // ---
        // make words str from float
        $words = number_format($words);
        // ---
        $use = rawurlEncode($user);
        $use = str_replace('+', '_', $use);

        $trs .= <<<HTML
            <tr>
                <td>$numb</td>
                <td><a href='leaderboard.php?get=users&user=$use'>$user</a></td>
                <td>$usercount</td>
                <td>$words</td>
                <td>$views</td>
            </tr>
            HTML;
    };

    $text = <<<HTML
        <table class='table compact table-striped sortable table_text_left leaderboard_tables' style='margin-top: 0px !important;margin-bottom: 0px !important'>
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
    return $text;
}
