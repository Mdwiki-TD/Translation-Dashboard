<?PHP

namespace Leaderboard\Camps;

/*
Usage:
use function Leaderboard\Camps\camps_list;
use function Leaderboard\Camps\camps_list2;
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

function camps_list()
{
    // ---
    $articles_to_camps = get_articles_to_camps();
    // ---
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
    // ---
    foreach ($articles_to_camps as $member => $camps) {
        // ---
        sort($camps);
        // ---
        $count = count($camps);
        // ---
        $table .= <<<HTML
            <tr>
                <td>$member</td>
                <td>$count</td>
                <td>
                    <ul>
        HTML;
        // ---
        foreach ($camps as $camp) {
            $table .= "<li>$camp</li>";
        };
        // ---
        $table .= <<<HTML
                    </ul>
                </td>
            </tr>
        HTML;
        // ---
    };
    // ---
    $table .= <<<HTML
            </tbody>
        </table>
    HTML;
    // ---
    echo $table;
    // ---
}

function shared_campaigns_table()
{
    $articles_to_camps = get_articles_to_camps();

    $shared_counts = [];

    foreach ($articles_to_camps as $article => $camps) {
        $camps = array_unique($camps);
        sort($camps);

        $count = count($camps);
        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                $pair_key = $camps[$i] . ' || ' . $camps[$j];
                if (!isset($shared_counts[$pair_key])) {
                    $shared_counts[$pair_key] = 0;
                }
                $shared_counts[$pair_key]++;
            }
        }
    }

    ksort($shared_counts);

    $table = <<<HTML
        <h3>الحملات المشتركة في المقالات</h3>
        <table class='table table-bordered sortable'>
            <thead>
                <tr>
                    <th>Campaign A</th>
                    <th>Campaign B</th>
                    <th>Shared Articles</th>
                </tr>
            </thead>
            <tbody>
    HTML;

    foreach ($shared_counts as $pair => $count) {
        list($campA, $campB) = explode(' || ', $pair);
        $table .= <<<HTML
            <tr>
                <td>$campA</td>
                <td>$campB</td>
                <td>$count</td>
            </tr>
        HTML;
    }

    $table .= <<<HTML
            </tbody>
        </table>
    HTML;

    echo $table;
}

function single_campaign_articles_table()
{
    $articles_to_camps = get_articles_to_camps();

    $single_articles = [];

    foreach ($articles_to_camps as $article => $camps) {
        if (count($camps) === 1) {
            $single_articles[$article] = $camps[0];
        }
    }

    ksort($single_articles);

    $table = <<<HTML
        <h3>المقالات المرتبطة بحملة واحدة فقط</h3>
        <table class='table table-striped sortable'>
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Campaign</th>
                </tr>
            </thead>
            <tbody>
    HTML;

    foreach ($single_articles as $article => $camp) {
        $table .= <<<HTML
            <tr>
                <td>$article</td>
                <td>$camp</td>
            </tr>
        HTML;
    }

    $table .= <<<HTML
            </tbody>
        </table>
    HTML;

    echo $table;
}

function camps_list2()
{
    $articles_to_camps = get_articles_to_camps();

    $shared_counts = [];

    foreach ($articles_to_camps as $article => $camps) {
        $camps = array_unique($camps);
        sort($camps); // ترتيب أبجدي لضمان تطابق المفاتيح
        $pair_key = implode(', ', $camps);

        if (!isset($shared_counts[$pair_key])) {
            $shared_counts[$pair_key] = 0;
        }
        $shared_counts[$pair_key]++;
    }


    ksort($shared_counts); // ترتيب النتائج

    // بناء الجدول
    $table = <<<HTML
        <table class='table table-striped soro'>
            <thead>
                <tr>
                    <th>Campaign</th>
                    <th>Articles</th>
                </tr>
            </thead>
            <tbody>
    HTML;

    foreach ($shared_counts as $pair => $count) {
        $table .= <<<HTML
            <tr>
                <td>$pair</td>
                <td>$count</td>
            </tr>
        HTML;
    }

    $table .= <<<HTML
            </tbody>
        </table>
    HTML;

    echo <<<HTML
        <div class="container-fluid col-9">
            <div class="card card2">
                <div class="card-header">
                    <span class="card-title" style="font-weight:bold;">
                        Camps
                    </span>
                    <div class="card-tools">
                        <button type="button" class="btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body1 card2">
                    $table
                </div>
            </div>
        </div>
    HTML;;
}
