<?php
// Define root path
include_once __DIR__ . '/../header.php';
include_once __DIR__ . '/../actions/functions.php';
include_once __DIR__ . '/../actions/html.php';
include_once __DIR__ . '/../actions/mdwiki_sql.php';
include_once __DIR__ . '/../results/tr_link.php';
include_once __DIR__ . '/inserter.php';

use function Actions\Html\login_card;
use function Results\TrLink\make_translate_link_medwiki;
use function Translate\Inserter\insertPage;

$coden = strtolower($_GET['code']);
$title_o = $_GET['title'] ?? "";
$useree = (global_username != '') ? global_username : '';

function go_to_translate_url($go, $title_o, $coden, $tr_type, $cat, $camp, $test)
{
    // ---
    $url = make_translate_link_medwiki($title_o, $coden, $cat, $camp, $tr_type);
    // ---
    echo <<<HTML
        <br>
        <h2>
            <a href='$url'>Click here to go to ContentTranslation in medwiki</a>
        </h2>
    HTML;
    // ---
    if (empty($test)) {
        echo <<<HTML
            <script type='text/javascript'>
            window.open('$url', '_self');
            </script>
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
$go = $_GET['go'] ?? '';
$go = (!empty($go)) ? true : false;


if (!empty($title_o) && !empty($coden) && $user_valid) {
    $title_o = trim($title_o);
    $coden   = trim($coden);
    $useree  = trim($useree);
    //  title=COVID-19&code=ady&cat=RTTCovid&camp=COVID&type=lead
    $test    = $_GET['test'] ?? '';
    $cat     = $_GET['cat'] ?? '';
    $camp    = $_GET['camp'] ?? '';
    $tr_type = $_GET['type'] ?? 'lead';
    // ---
    $useree  = rawurldecode($useree);
    $cat     = rawurldecode($cat);
    $camp    = rawurldecode($camp);
    $title_o = rawurldecode($title_o);
    // ---
    $word = $Words_table[$title_o] ?? 0;
    // ---
    if ($tr_type == 'all') {
        $word = $All_Words_table[$title_o] ?? 0;
    }
    // ---
    insertPage($title_o, $word, $tr_type, $cat, $coden, $useree);
    // ---
    go_to_translate_url($go, $title_o, $coden, $tr_type, $cat, $camp, $test);
}

echo '</div>';
include_once __DIR__ . '/../foter.php';
