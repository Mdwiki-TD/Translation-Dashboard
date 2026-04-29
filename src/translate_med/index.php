<?php

if (isset($_REQUEST['test']) || isset($_COOKIE['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

include_once __DIR__ . '/../backend/userinfos_wrap.php';
include_once __DIR__ . '/../include_all.php';
include_once __DIR__ . '/../backend/others/db_insert.php';

use function Results\TrLink\make_ContentTranslation_url;
// use function TranslateMed\Inserter\insertPage;
use function TranslateMed\Inserter\insertPage_inprocess;
use function SQLorAPI\GetDataTab\get_td_or_sql_users_no_inprocess;

function go_to_translate_url($title_o, $coden, $tr_type, $cat, $camp)
{
    // ---
    $test = $_GET['test'] ?? '';
    // ---
    $url = make_ContentTranslation_url($title_o, $coden, $cat, $camp, $tr_type);
    // ---
    echo <<<HTML
        <br>
        <h2>
            <a target="_blank" href='$url'>Click here to go to ContentTranslation in medwiki</a>
        </h2>
    HTML;
    // ---
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

$coden = strtolower(filter_input(INPUT_GET, 'code', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');
$title_o = filter_input(INPUT_GET, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
$useree = !empty($GLOBALS['global_username']) ? $GLOBALS['global_username'] : '';

if (empty($useree)) {
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

if (!empty($title_o) && !empty($coden)) {
    // ---
    $users_no_inprocess = get_td_or_sql_users_no_inprocess();
    $users_no_inprocess = array_column($users_no_inprocess, 'is_active', 'user');
    // ---
    $title_o = trim($title_o);
    $coden   = trim($coden);
    $useree  = trim($useree);
    //  title=COVID-19&code=ady&cat=RTTCovid&camp=COVID&type=lead
    // ---
    $cat = filter_input(INPUT_GET, 'cat', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
    $camp = filter_input(INPUT_GET, 'camp', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
    $tr_type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'lead';
    $word = filter_input(INPUT_GET, 'word', FILTER_VALIDATE_INT, [
        'options' => ['default' => 0, 'min_range' => 0]
    ]);
    // ---
    $user_decoded  = rawurldecode($useree);
    $cat     = rawurldecode($cat);
    $camp    = rawurldecode($camp);
    $title_o = rawurldecode($title_o);
    // ---
    if (($users_no_inprocess[$useree] ?? 0) != 1) {
        insertPage_inprocess($title_o, $word, $tr_type, $cat, $coden, $user_decoded);
    }
    // ---
    go_to_translate_url($title_o, $coden, $tr_type, $cat, $camp);
}
// ---
echo <<<HTML
    </div>

    </div>
    </main>
</body>

</html>
HTML;
