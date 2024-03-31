<?php
// ---
if (isset($_REQUEST['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};
//---
include_once '../header.php';
include_once root_dir . '/tables.php';
include_once root_dir . '/functions.php';
include_once root_dir . '/enwiki/td1.php';
// ---
$coden = strtolower($_GET['code'] ?? '');
$title_o = $_GET['title'] ?? 'Dravet syndrome';
// ---
$tit_line = make_input_group( 'title', 'title', $title_o, 'required');
$cod_line = make_input_group( 'code', 'code', $coden, 'required');
// ---
$ref_line  = make_form_check_input('fix ref', 'refs_fix', 1, 0, ($_GET['refs_fix'] ?? '') == 1 ? 'checked' : '');
$text_line = make_form_check_input('fix text', 'text_fix', 1, 0, ($_GET['text_fix'] ?? '') == 1 ? 'checked' : '');
// ---
$testin = (($_REQUEST['test'] ?? '') != '') ? "<input name='test' value='1' hidden/>" : "";
// ---
$nana = <<<HTML
    <div class='card' style='font-weight: bold;'>
        <div class='card-body'>
            <div class='row'>
                <div class='col-md-10 col-md-offset-1'>
                    <form action='index.php' method='GET'>
                        $testin
                        $tit_line
                        $cod_line
                        $ref_line
                        $text_line
                        <input class='btn btn-outline-primary' type='submit' name='start' value='Start' />
                    </form>
                </div>
            </div>
        </div>
    </div>
    HTML;

echo $nana;

if ($title_o != '' && $coden != '') {
    $title_o = trim($title_o);
    $coden   = trim($coden);
    // ---
    $text_fix = ($_GET['text_fix'] ?? '') == 1 ? true : false;
    $refs_fix = ($_GET['refs_fix'] ?? '') == 1 ? true : false;
    // ---
    test_print("enwiki/index.php; title: $title_o; code: $coden");
    // ---
    $test    = $_GET['test'] ?? '';
    $cat     = $_GET['cat'] ?? '';
    $fixref  = $_GET['fixref'] ?? '';
    $tr_type = $_GET['type'] ?? 'lead';
    // ---
    $wholearticle = $tr_type == 'all' ? true : false;
    //---
    if ($test == 00) {
        startTranslatePhp($title_o, $tr_type);
    } else {
        $newtext = prase_text($title_o, $wholearticle, $text_fix=$text_fix, $refs_fix=$refs_fix);
        // ---
        echo "<pre>" . htmlentities($newtext). "</pre>";
        // ---
    }
};

echo '</div>';


include_once root_dir . '/foter.php';
    

?>
