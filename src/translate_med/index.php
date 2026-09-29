<?php

include_once dirname(__DIR__) . '/bootstrap.php';

use App\User\CurrentUser;
use App\MdwikiSql\Database;

use function App\Results\TrLink\make_ContentTranslation_url;
use function App\SQLorAPI\GetDataTab\get_td_or_sql_users_no_inprocess;
use function App\SQLorAPI\GetDataTab\get_td_or_sql_categories;

function insertPageInprocess($title, $word, $tr_type, $cat, $lang, $user): bool
{

    $quae_new = <<<SQL
        INSERT INTO in_process (title, user, lang, cat, translate_type, word, add_date)
        SELECT ?, ?, ?, ?, ?, ?, DATE(NOW())
        WHERE NOT EXISTS
            (SELECT 1
            FROM in_process
            WHERE title = ?
            AND lang = ?
            AND user = ?
        )
    SQL;

    $params = [$title, $user, $lang, $cat, $tr_type, $word, $title, $lang, $user];

    $db = new Database();
    $db->testPrint($quae_new);

    return $db->executequery($quae_new, $params);
};

function go_to_translate_url($title_o, $coden, $tr_type, $cat, $camp)
{

    $test = $_GET['test'] ?? '';

    $url = make_ContentTranslation_url(
        $title_o,
        $coden,
        $cat,
        $camp,
        $tr_type,
    );

    echo <<<HTML
        <br>
        <h2>
            <a target="_blank" href='$url'>Click here to go to ContentTranslation in medwiki</a>
        </h2>
    HTML;

    if (empty($test)) {
        echo <<<HTML
            <script type='text/javascript'>
            window.open('$url', '_self');
            </script>
            <meta http-equiv='refresh' content='0; url=$url'>
            <noscript>
                <meta http-equiv='refresh' content='0; url=$url'>
            </noscript>
        HTML;
    }
}

$currentUser = CurrentUser::getInstance();


$coden = strtolower(filter_input(INPUT_GET, 'code', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');
$title_o = filter_input(INPUT_GET, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';

if (!$currentUser->isLoggedIn()) {
    echo <<<HTML
        <div class='card' style='font-weight: bold;'>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-md-10'>
                        <a role='button' class='btn btn-outline-primary' href='/auth/login.php'>
                            <i class='fas fa-sign-in-alt fa-sm fa-fw mr-1'></i><span class='navtitles'>Login</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    HTML;
    exit;
}

$useree = $currentUser->getUsername();

if (!empty($title_o) && !empty($coden)) {

    // use function App\SQLorAPI\GetDataTab\get_td_or_sql_categories;
    $categories_tab = get_td_or_sql_categories();
    $cats_data = array_column($categories_tab, "campaign", "category");

    $users_no_inprocess = get_td_or_sql_users_no_inprocess();
    $users_no_inprocess = array_column($users_no_inprocess, 'is_active', 'user');

    $title_o = trim($title_o);
    $coden   = trim($coden);
    //  title=COVID-19&code=ady&cat=RTTCovid&camp=COVID&type=lead

    $cat = filter_input(INPUT_GET, 'cat', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
    $camp = filter_input(INPUT_GET, 'camp', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';

    $tr_type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? filter_input(INPUT_GET, 'tr_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'lead';

    $word = filter_input(INPUT_GET, 'word', FILTER_VALIDATE_INT, [
        'options' => ['default' => 0, 'min_range' => 0]
    ]);

    if (empty($camp) && !empty($cat)) {
        $camp = $cats_data[$cat] ?? "";
    }

    $user_decoded  = rawurldecode($useree);
    $cat     = rawurldecode($cat);
    $title_o = rawurldecode($title_o);

    $camp    = rawurldecode($camp);
    if (($users_no_inprocess[$useree] ?? 0) != 1) {
        insertPageInprocess($title_o, $word, $tr_type, $cat, $coden, $user_decoded);
    }

    go_to_translate_url(
        $title_o,
        $coden,
        $tr_type,
        $cat,
        $camp,
    );
}

echo <<<HTML
    </div> </div> </main> </body> </html>
HTML;
