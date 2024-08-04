<?php
// Define root path
use function Actions\Html\login_card;
use function Actions\Html\make_input_group;
use function Actions\Html\make_mdwiki_title;
use function Actions\Html\make_translation_url;
use function Actions\Functions\escape_string;
use function Actions\MdwikiSql\execute_query;
use function Translate\EnAPI\Find_pages_exists_or_not;
use function Translate\Translator\startTranslatePhp;
use function Actions\Html\make_target_url;

$pathParts = explode('public_html', __FILE__);
// the root path is the first part of the split file path
$ROOT_PATH = $pathParts[0];

$fix_ref_in_text = $settings['fix_ref_in_text']['value'] ?? '0';
$fix_ref_in_text = ($fix_ref_in_text == "1") ? true : false;

// Get parameters from the URL
$coden = strtolower($_GET['code']);
$title_o = $_GET['title'] ?? "";
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
    if ($useree == "") {
        return;
    }
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
if ($useree == '') echo login_card();
//---
function go_to_translate_url($output, $go, $title_o, $coden, $tr_type, $test)
{
    // ---
    $url = make_translation_url($title_o, $coden, $tr_type);
    $title_o2 = rawurlencode(str_replace(' ', '_', $title_o));
    // ---
    $page_en = $tr_type == 'all' ? "User:Mr. Ibrahem/$title_o2/full" : "User:Mr. Ibrahem/$title_o2";
    // ---
    if ($coden == 'en') {
        $url = "//en.wikipedia.org/w/index.php?title=$page_en&action=edit";
    }
    // ---
    if (trim($output) == true || $go) {

        if ($test != "" && (!$go)) {
            echo <<<HTML
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
        // ---
        echo <<<HTML
            page: $li has no text..<br>
        HTML;
    } else {
        $en_link = make_target_url($page_en, "en", $name = $title_o);
        echo <<<HTML
            error when save to enwiki. $en_link.<br>($output)
        HTML;
    }
}
//---
$user_valid = ($useree != '') ? true : false;
$go = $_GET['go'] ?? '';
$go = ($go != '') ? true : false;
//---
// TODO: temporary solution
// $user_valid = true;
// $go = true;
// Process form data if title, code, and user are set
if ($title_o != '' && $coden != '' && $user_valid) {
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
    // ---
    $title2 = 'User:Mr. Ibrahem/' . $title_o;
    // ---
    $output = false;
    // ---
    // $output = startTranslatePhp($title_o, $tr_type, false, $expend_refs = $fix_ref_in_text);
    // ---
    if ($output != true) {
        $output = Find_pages_exists_or_not($title2);
    };
    // ---
    echo $output;
    // ---
    if ($output == true) {
        insertPage($title_o, $word, $tr_type, $cat, $camp, $coden, $useree, $test);
    }
    // ---
    go_to_translate_url($output, $go, $title_o, $coden, $tr_type, $test);
}
