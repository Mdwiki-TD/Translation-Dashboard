<?PHP

namespace Leaderboard\Camps;

/*
Usage:
use function Leaderboard\Camps\get_articles_to_camps;

*/

use function SQLorAPI\GetDataTab\get_td_or_sql_categories_members;
use function SQLorAPI\GetDataTab\get_td_or_sql_categories;

function get_articles_to_camps()
{
    static $articles_to_camps = [];
    // ---
    if (!empty($articles_to_camps)) return $articles_to_camps;
    // ---
    $categories_tab = get_td_or_sql_categories();
    $cats_data = array_column($categories_tab, "campaign", "category");
    // ---
    if (isset($cats_data['RTT'])) {
        unset($cats_data['RTT']);
        $cats_data['RTT'] = 'Main';
    }
    // ---
    foreach ($cats_data as $cat => $camp) {
        // ---
        $members = get_td_or_sql_categories_members($cat);
        // ---
        foreach ($members as $member) {
            // ---
            if (empty($member)) continue;
            // ---
            if (!isset($articles_to_camps[$member])) $articles_to_camps[$member] = [];
            // ---
            $articles_to_camps[$member][] = $camp;
        }
    };
    // ---
    return $articles_to_camps;
}
