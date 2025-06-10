<?PHP

namespace Leaderboard\Camps;

/*
Usage:
use function Leaderboard\Camps\get_articles_to_camps;

*/

use function Results\GetCats\get_category_from_cache;
use Tables\SqlTables\TablesSql;

function get_articles_to_camps()
{
    static $articles_to_camps = [];
    // ---
    if (!empty($articles_to_camps)) return $articles_to_camps;
    // ---
    // sort TablesSql::$s_cat_to_camp make RTT last item
    $cat2camp = TablesSql::$s_cat_to_camp;
    // ---
    if (isset($cat2camp['RTT'])) {
        unset($cat2camp['RTT']);
        $cat2camp['RTT'] = 'Main';
    }
    // ---
    foreach ($cat2camp as $cat => $camp) {
        // ---
        $members = get_category_from_cache($cat);
        // ---
        foreach ($members as $member) {
            // ---
            if (!isset($articles_to_camps[$member])) $articles_to_camps[$member] = [];
            // ---
            $articles_to_camps[$member][] = $camp;
        }
    };
    // ---
    return $articles_to_camps;
}
