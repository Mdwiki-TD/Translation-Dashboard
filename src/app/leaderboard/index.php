<?PHP


use App\User\CurrentUser;
use App\Settings\Settings;
use function App\Leaderboard\Graph\print_graph_tab;
use function App\Leaderboard\Graph2\print_graph_tab_2_new;
use function App\Leaderboard\Index\main_leaderboard;
use function App\Leaderboard\CampText\echo_html;
use function App\Leaderboard\Langs\langs_html;
use function App\Leaderboard\Users\users_html;

use function App\SQLorAPI\GetDataTab\get_td_or_sql_titles_infos;
use function App\SQLorAPI\GetDataTab\get_td_or_sql_categories;
use function App\SQLorAPI\GetDataTab\get_endpoint;
use function App\SQLorAPI\GetDataTab\get_td_or_sql_langs;
use function App\SQLorAPI\Funcs\get_graph_data;

$currentUser = CurrentUser::getInstance();
$global_username = $currentUser->getUsername();

$endpoint = get_endpoint();

echo <<<HTML
<style>
.border_debugx {
    border: 1px solid;
    border-radius: 5px;
}
</style>
HTML;

$get = filter_input(INPUT_GET, 'get', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';

$langcode = filter_input(INPUT_GET, 'langcode', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
$mainlang = filter_input(INPUT_GET, 'lang', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'All';

$user_to_html = filter_input(INPUT_GET, 'user', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
$user_to_curl = filter_input(INPUT_GET, 'user', FILTER_UNSAFE_RAW) ?? '';

$year_y   = filter_input(INPUT_GET, 'year', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'All';
$month_y  = filter_input(INPUT_GET, 'month', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
$camp     = filter_input(INPUT_GET, 'camp', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'All';

$_titles_infos   = get_td_or_sql_titles_infos();
$categories_tab = get_td_or_sql_categories();

$lead_words_table = array_column($_titles_infos, 'w_lead_words', 'title');
$cats_data = array_column($categories_tab, "campaign", "category");


if ($get == 'users' || !empty($user_to_curl)) {

    echo users_html(
        $mainlang,
        $year_y,
        $camp,
        $user_to_curl,
        $user_to_html,
        $global_username,
        $lead_words_table,
        $cats_data,
        $endpoint
    );
} elseif ($get == 'langs' || !empty($langcode)) {

    echo langs_html(
        $langcode,
        $year_y,
        $camp,
        $lead_words_table,
        $cats_data,
        $endpoint
    );
} elseif (!empty($_GET['camps'] ?? '')) {
    // http://localhost:9001/Translation_Dashboard/leaderboard.php?camps=1&test=1

    echo echo_html();
} elseif (!empty($_GET['graph'] ?? '')) {
    // http://localhost:9001/Translation_Dashboard/leaderboard.php?graph=1&test=1

    $data = get_graph_data();
    echo print_graph_tab($data);
} elseif (!empty($_GET['graph_api'] ?? '')) {
    // http://localhost:9001/Translation_Dashboard/leaderboard.php?graph_api=1&test=1

    echo print_graph_tab_2_new();
} else {

    $user_group = filter_input(INPUT_GET, 'project', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
        ?? filter_input(INPUT_GET, 'user_group', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
        ?? 'all';

    $langs_data = get_td_or_sql_langs();

    $settings = Settings::getInstance();
    $addcat = !$settings->isProduction() && (isset($_GET['nocat']));

    echo main_leaderboard($year_y, $camp, $user_group, $langs_data, $addcat, $month_y);
}
