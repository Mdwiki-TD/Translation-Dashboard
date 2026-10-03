<?php

namespace App\Leaderboard\Helpers\Users;

use App\Utils\Html;

class LeaderTablesUsers
{
    public static function module_copy_data($users_tab)
    {
        $lal = "<textarea cols='55' rows='10' id='users_targets' name='users_targets'>";

        foreach ($users_tab as $tab) {
            $user = $tab['user'];
            $lang = $tab['lang'];

            if (empty($lang) || empty($user)) continue;

            $lal .= "#{{#target:User:$user|$lang.wikipedia.org}}\n";
        }

        $lal .= '</textarea>';

        $modal = Html::make_modal_fade('', $lal, 'targets', '<a class="btn btn-outline-primary" onclick="copy_target_text(\'users_targets\')">Copy</a>');

        return $modal;
    }

    public static function makeUsersTable($users, $min = 2)
    {
        uasort($users, function ($a, $b) {
            return $b["count"] <=> $a["count"];
        });

        $numb = 0;
        $trs = "";

        foreach ($users as $user => $tab) {
            $numb += 1;

            $usercount = number_format($tab['count'] ?? 0);
            $views = number_format($tab['views'] ?? 0);

            $words = $tab['words'] ?? 0;

            $words = number_format($words);

            $use = rawurlencode($user);
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
        }

        return <<<HTML
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
    }
}
