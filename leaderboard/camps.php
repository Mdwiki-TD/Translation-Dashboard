<?PHP

namespace Leaderboard\Camps;

/*
(\$)(articles_to_camps|camps_to_articles)

CampsTabs::$1$2

Usage:
use Leaderboard\Camps\CampsTabs;
use function Leaderboard\Camps\camps_list;

*/

use function Results\GetCats\get_category_from_cache;
use Tables\SqlTables\TablesSql;

class CampsTabs
{
    public static $articles_to_camps = [];
    public static $camps_to_articles = [];
}

// sort TablesSql::$s_cat_to_camp make RTT last item
$cat2camp = TablesSql::$s_cat_to_camp;
//---
if (isset($cat2camp['RTT'])) {
    unset($cat2camp['RTT']);
    $cat2camp['RTT'] = 'Main';
}
//---
$members_done = [];
//---
foreach ($cat2camp as $cat => $camp) {
    CampsTabs::$camps_to_articles[$camp] = [];
    //---
    $members = get_category_from_cache($cat);
    //---
    foreach ($members as $member) {
        //---
        if (!isset(CampsTabs::$articles_to_camps[$member])) CampsTabs::$articles_to_camps[$member] = [];
        //---
        CampsTabs::$articles_to_camps[$member][] = $camp;
        //---
        if (in_array($member, $members_done)) continue;
        $members_done[] = $member;
        //---
        if (!in_array($member, CampsTabs::$camps_to_articles[$camp])) {
            CampsTabs::$camps_to_articles[$camp][] = $member;
        }
        //---
    }
};

function camps_list()
{
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
    foreach (CampsTabs::$articles_to_camps as $member => $camps) {
        sort($camps);
        CampsTabs::$articles_to_camps[$member] = $camps;
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
