<?PHP

namespace Leaderboard\Camps;

/*
Usage:
use function Leaderboard\Camps\camps_list;

*/

use function Results\GetCats\get_cat_from_cache;

// $cat_to_camp
$articles_to_camps = [];
$camps_to_articles = [];
//---
// sort $cat_to_camp make RTT last item
$cat_to_camp2 = $cat_to_camp;
//---
if (isset($cat_to_camp2['RTT'])) {
    unset($cat_to_camp2['RTT']);
    $cat_to_camp2['RTT'] = 'Main';
}
//---
$members_done = [];
//---
foreach ($cat_to_camp2 as $cat => $camp) {
    $camps_to_articles[$camp] = [];
    //---
    $members = get_cat_from_cache($cat);
    //---
    foreach ($members as $member) {
        //---
        if (!isset($articles_to_camps[$member])) $articles_to_camps[$member] = [];
        //---
        $articles_to_camps[$member][] = $camp;
        //---
        if (in_array($member, $members_done)) continue;
        $members_done[] = $member;
        //---
        if (!in_array($member, $camps_to_articles[$camp])) {
            $camps_to_articles[$camp][] = $member;
        }
        //---
    }
};
//---
function camps_list()
{
    global $articles_to_camps;
    $table = <<<HTML
        <table class='table table-striped sortable'>
            <thead>
                <tr>
                    <th>Member</th>
                    <th>lenth</th>
                    <th>Campaigns</th>
                </tr>
            </thead>
            <tbody>
    HTML;
    foreach ($articles_to_camps as $member => $camps) {
        sort($camps);
        $articles_to_camps[$member] = $camps;
        $count = count($camps);
        $table .= <<<HTML
                <tr>
                    <td>$member</td>
                    <td>$count</td>
                    <td>
                        <ul>
        HTML;
        foreach ($camps as $camp) {
            $table .= <<<HTML
                            <li>$camp</li>
        HTML;
        };
        $table .= <<<HTML
                        </ul>
                    </td>
                </tr>
        HTML;
    };
    $table .= <<<HTML
            </tbody>
        </table>
    HTML;
    //---
    echo $table;
    //---
}
