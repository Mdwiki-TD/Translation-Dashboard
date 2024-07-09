<?PHP
//---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//---
include_once __DIR__ . '/header.php';
// include_once __DIR__ . '/Tables/tables.php';
include_once __DIR__ . '/actions/functions.php';
include_once __DIR__ . '/enwiki/td1.php';
// include_once __DIR__ . '/Tables/langcode.php';
// ---
$title = $_GET['title'] ?? '';
// ---
$tit_line = make_input_group('title', 'title', $title, 'required');
// ---
$ref_line  = make_form_check_input('fix ref', 'refs_fix', 1, 0, ($_GET['refs_fix'] ?? '') == 1 ? 'checked' : '');
$text_line = make_form_check_input('fix text', 'text_fix', 1, 0, ($_GET['text_fix'] ?? '') == 1 ? 'checked' : '');
$type_line = make_form_check_input('full?', 'tr_type', 1, 0, ($_GET['tr_type'] ?? '') == 1 ? 'checked' : '');
$testin    = make_form_check_input('test', 'test', 1, 0, ($_GET['test'] ?? '') == 1 ? 'checked' : '');
// ---
$nana = <<<HTML
    <form action='enwiki.php' method='GET'>
        <div class='row'>
            $tit_line
            <div class='col-md-4'>
                $ref_line
                $text_line
            </div>
            <div class='col-md-4'>
                $type_line
                $testin
            </div>
        </div>
        <div class='row'>
            <div class='col-md-4'>
                <input class='btn btn-outline-primary' type='submit' name='start' value='Start' />
            </div>
        </div>
    </form>
HTML;


$new_text = "";

if ($title != '') {
    $title = trim($title);
    // ---
    $text_fix = ($_GET['text_fix'] ?? '') == 1 ? true : false;
    $refs_fix = ($_GET['refs_fix'] ?? '') == 1 ? true : false;
    // ---
    test_print("enwiki/index.php; title: $title");
    // ---
    $test    = $_GET['test'] ?? '';
    $cat     = $_GET['cat'] ?? '';
    $fixref  = $_GET['fixref'] ?? '';
    $tr_type = ($_GET['tr_type'] ?? '') == 1 ? 'all' : 'lead';
    // ---
    $wholearticle = $tr_type == 'all' ? true : false;
    //---
    $newtext = startTranslatePhp($title, $tr_type, true, $do_fix_refs = $refs_fix);
    //---
    // trim
    $newtext = trim($newtext);
    //---
    $new_text = htmlentities($newtext);
};

if ($new_text != '') {
    $new_text = <<<HTML
        new text:
        <textarea class="form-control" cols="20" rows="10">$new_text</textarea>
    HTML;
}
$articleurl = "https://mdwiki.org/w/index.php?title=$title";
echo <<<HTML
    <div class='container'>
        <div class='card'>
            <div class='card-header' style='font-weight: bold;'>
                test get text from mdwiki.org and do changes on it before send it to en.wikipedia.org:
            </div>
            <div class='card-body'>
                <div class="container">
                    $nana
                </div>
            </div>
        </div>
        <hr />
        <div class='card'>
            <div class="card-header aligncenter" style="font-weight:bold;">
                <h3>
                    page: <a target='_blank' href='$articleurl'>$title</a>
                </h3>
            </div>
            <div class='card-body'>
                $new_text
            </div>
            <div class='card-footer'>
                done.
            </div>
        </div>
    </div>
    <br>
HTML;

include_once __DIR__ . '/foter.php';
