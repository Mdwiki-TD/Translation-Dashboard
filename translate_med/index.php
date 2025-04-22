<?php
// Define root path
include_once __DIR__ . '/../../auth/auth/user_infos.php';
// include_once __DIR__ . '/../header.php';
// include_once __DIR__ . '/../actions/load_request.php';
include_once __DIR__ . '/../actions/html.php';
include_once __DIR__ . '/../Tables/sql_tables.php';
include_once __DIR__ . '/../results/tr_link.php';
include_once __DIR__ . '/db_insert.php';
include_once __DIR__ . '/../api_or_sql/index.php';

use function Actions\Html\login_card;
use function Results\TrLink\make_translate_link_medwiki;
use function TranslateMed\Inserter\insertPage;
use function TranslateMed\Inserter\insertPage_inprocess;
use function SQLorAPI\GetDataTab\get_td_or_sql_users_no_inprocess;

$coden = strtolower($_GET['code']);
$title_o = $_GET['title'] ?? "";
$useree = ($GLOBALS['global_username'] != '') ? $GLOBALS['global_username'] : '';

$users_no_inprocess = get_td_or_sql_users_no_inprocess();
$users_no_inprocess = array_column($users_no_inprocess, 'user');
// var_export($users_no_inprocess);

function go_to_translate_url($title_o, $coden, $tr_type, $cat, $camp)
{
    // ---
    $test = $_GET['test'] ?? '';
    // ---
    $url = make_translate_link_medwiki($title_o, $coden, $cat, $camp, $tr_type);
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

if (empty($useree)) {
    echo login_card();
    exit;
}

$user_valid = (!empty($useree)) ? true : false;

if (!empty($title_o) && !empty($coden) && $user_valid) {
    $title_o = trim($title_o);
    $coden   = trim($coden);
    $useree  = trim($useree);
    //  title=COVID-19&code=ady&cat=RTTCovid&camp=COVID&type=lead
    $cat     = $_GET['cat'] ?? '';
    $camp    = $_GET['camp'] ?? '';
    $tr_type = $_GET['type'] ?? 'lead';
    $word    = $_GET['word'] ?? 0;
    // ---
    $user_decoded  = rawurldecode($useree);
    $cat     = rawurldecode($cat);
    $camp    = rawurldecode($camp);
    $title_o = rawurldecode($title_o);
    // ---
    // if not user in $users_no_inprocess
    if (!in_array($useree, $users_no_inprocess)) {
        insertPage($title_o, $word, $tr_type, $cat, $coden, $user_decoded);
        // ---
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
// include_once __DIR__ . '/../footer.php';
