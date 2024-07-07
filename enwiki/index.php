<?php
// ---
if (isset($_REQUEST['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};
//---
include_once '../header.php';
include_once root_dir . '/Tables/tables.php';
include_once root_dir . '/actions/functions.php';
include_once root_dir . '/enwiki/td1.php';
include_once root_dir . '/Tables/langcode.php';
// ---
$coden = strtolower($_GET['code'] ?? '');
$title_o = $_GET['title'] ?? 'Dravet syndrome';
// ---
$tit_line = make_input_group( 'title', 'title', $title_o, 'required');
$cod_line = make_input_group( 'code', 'code', $coden, 'required');
// ---
$ref_line  = make_form_check_input('fix ref', 'refs_fix', 1, 0, ($_GET['refs_fix'] ?? '') == 1 ? 'checked' : '');
$text_line = make_form_check_input('fix text', 'text_fix', 1, 0, ($_GET['text_fix'] ?? '') == 1 ? 'checked' : '');
$type_line = make_form_check_input('full?', 'tr_type', 1, 0, ($_GET['tr_type'] ?? '') == 1 ? 'checked' : '');
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
                        $type_line
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
    $tr_type = ($_GET['tr_type'] ?? '') == 1 ? 'all' : 'lead';
    // ---
    $wholearticle = $tr_type == 'all' ? true : false;
    //---
    echo "<br>startTranslatePhp:";
    //---
    $newtext = startTranslatePhp($title_o, $tr_type, true, $do_fix_refs=$refs_fix);
    //---
    $new_text = htmlentities($newtext);
    //---
    // echo "<pre>" . htmlentities($newtext). "</pre>";
    //---
    echo <<<HTML
        new text:<br>
        <textarea class="form-control" cols="20" rows="25">$new_text
        </textarea>
    HTML;
};

echo '</div>';


include_once root_dir . '/foter.php';


?>
