<?php
// Consolidate includes
include_once __DIR__ . '/header.php';
include_once __DIR__ . '/Tables/tables.php';
include_once __DIR__ . '/actions/functions.php';
include_once __DIR__ . '/enwiki/td1.php';
include_once __DIR__ . '/actions/html.php';
include_once __DIR__ . '/Tables/sql_tables.php';

// Define root path
$pathParts = explode('public_html', __FILE__);
// the root path is the first part of the split file path
$ROOT_PATH = $pathParts[0];

$fix_ref_in_text = $settings['fix_ref_in_text']['value'] ?? '0';

$fix_ref_in_text = ($fix_ref_in_text == "1") ? true : false;

// Get parameters from the URL
$coden = strtolower($_GET['code']);
$title_o = $_GET['title'];
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

// Function to insert page into the database
function insertPage($title_o, $word, $tr_type, $cat, $camp, $coden, $useree, $test)
{
    $useree  = escape_string($useree);
    $cat     = escape_string($cat);
    $title_o = escape_string($title_o);

    $quae_new = <<<SQL
        INSERT INTO pages (title, word, translate_type, cat, lang, date, user, pupdate, target, add_date)
        SELECT ?, ?, ?, ?, ?, now(), ?, '', '', now()
        WHERE NOT EXISTS
            (SELECT 1
            FROM pages
            WHERE title = ?
            AND lang = ?
            AND user = ?
        )
    SQL;

    $params = [$title_o, $word, $tr_type, $cat, $coden, $useree, $title_o, $coden, $useree];
    if ($test != '') {
        echo "<br>$quae_new<br>";
    }
    execute_query($quae_new, $params = $params);
}

// Display login button if user is not logged in
if ($useree == '') {
    echo login_card();
}

// Process form data if title, code, and user are set
if ($title_o != '' && $coden != '' && $useree != '') {
    $nana = '';
    $title_o = trim($title_o);
    $coden   = trim($coden);
    $useree  = trim($useree);

    $test    = $_GET['test'] ?? '';
    $cat     = $_GET['cat'] ?? '';
    $camp    = $_GET['camp'] ?? '';
    $fixref  = $_GET['fixref'] ?? '';
    $tr_type = $_GET['type'] ?? 'lead';

    $useree  = rawurldecode($useree);
    $cat     = rawurldecode($cat);
    $camp    = rawurldecode($camp);
    $title_o = rawurldecode($title_o);

    $word = $Words_table[$title_o] ?? 0;

    if ($tr_type == 'all') {
        $word = $All_Words_table[$title_o] ?? 0;
    }
    insertPage($title_o, $word, $tr_type, $cat, $camp, $coden, $useree, $test);
    // ---
    $output = startTranslatePhp($title_o, $tr_type, false, $do_fix_refs = $fix_ref_in_text);
    // ---
    if (trim($output) == 'true' || isset($_GET['go'])) {
        $url = make_translation_url($title_o, $coden, $tr_type);

        $title_o2 = rawurlencode(str_replace(' ', '_', $title_o));

        if ($coden == 'en') {
            $page = $tr_type == 'all' ? "User:Mr. Ibrahem/$title_o2/full" : "User:Mr. Ibrahem/$title_o2";
            //---
            $url = "//en.wikipedia.org/w/index.php?title=$page&action=edit";
        }

        if ($test != "" && (!isset($_GET['go']))) {
            echo <<<HTML
                $nana
                <br>trim($output) == true<br>
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
    } elseif (trim($output) == 'notext') {
        $li = make_mdwiki_title($title_o);
        echo <<<HTML
            $nana
            page: $li has no text..<br>
        HTML;
    } else {
        echo <<<HTML
            $nana
            save to enwiki: error..<br>($output)
        HTML;
    }
}

echo '</div>';
include_once __DIR__ . '/foter.php';
