<?php
// Define root path
use function Actions\Html\login_card;
use function Actions\Html\make_input_group;

// Get parameters from the URL
$coden = strtolower($_GET['code']);
$title_o = $_GET['title'] ?? "";
$cat     = $_GET['cat'] ?? ''; // cat_to_camp

// $useree  = (global_username != '') ? global_username : $_GET['username'];
$useree = (global_username != '') ? global_username : '';

// Display form if 'form' is set in the URL
if (isset($_GET['form'])) {
    $tit_line = make_input_group('title', 'title', $title_o, 'required');
    $cod_line = make_input_group('code', 'code', $coden, 'required');

    $nana = <<<HTML
        <div class='card' style='font-weight: bold;'>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-md-10 col-md-offset-1'>
                        <form action='translate.php' method='GET'>
                            $tit_line
                            $cod_line
                            <input class='btn btn-outline-primary' type='submit' name='start' value='Start' />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    HTML;
    echo $nana;
}

// Display login button if user is not logged in
if ($useree == '') echo login_card();

$user_valid = ($useree != '') ? true : false;

if ($title_o != '' && $coden != '' && $user_valid) {
    // ---
    $useree  = rawurldecode($useree);
    $coden   = rawurldecode($coden);
    $cat     = rawurldecode($cat);
    $title_o = rawurldecode($title_o);
    // ---
    $campain = $cat_to_camp[$cat] ?? $cat;
    // ---
    $title_o = trim($title_o);
    // ---
    $endpoint = "https://medwiki.toolforge.org/md/index.php";
    // ---
    // ?title=Special:ContentTranslation&from=mdwiki&to=ary&campaign=contributionsmenu&page=Dracunculiasis&targettitle=Dracunculiasis
    // ---
    $title_o = str_replace('%20', '_', $title_o);
    // ---
    $params = [
        'title' => 'Special:ContentTranslation',
        'from' => 'mdwiki',
        'to' => $coden,
        'campaign' => $campain,
        'page' => $title_o
    ];
    // ---
    $url = $endpoint . "?" . http_build_query($params);
    // ---
    if ($test != "") {
        echo <<<HTML
            start_tr<br>
            $url
        HTML;
    } else {
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
