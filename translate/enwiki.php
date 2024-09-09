<?PHP
//---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//---
include_once __DIR__ . '/../header.php';
include_once __DIR__ . '/../actions/functions.php';
include_once __DIR__ . '/translator.php';
// ---
use function Actions\Html\make_form_check_input;
use function Actions\Html\make_input_group_no_col;
use function Translate\Translator\startTranslatePhp;
use function Translate\Translator\TranslatePhpEditText;
use function Actions\Functions\test_print;

function make_form($text)
{
    // ---
    $tit_line  = make_input_group_no_col('title', 'title', $_POST['title'] ?? '', '');
    $ref_line  = make_form_check_input('expend refs', 'refs_expend', 1, 0, ($_POST['refs_expend'] ?? '') == 1 ? 'checked' : '');
    $type_line = make_form_check_input('full?', 'tr_type', 1, 0, ($_POST['tr_type'] ?? '') == 1 ? 'checked' : '');
    $testin    = make_form_check_input('test', 'test', 1, 0, ($_POST['test'] ?? '') == 1 ? 'checked' : '');
    // ---
    $nana = <<<HTML
        <form action='enwiki.php' method='POST'>
            <div class='row'>
                <div class='col-md-4'>
                    $tit_line
                    <input class='btn btn-outline-primary' type='submit' name='start' value='Start' />
                </div>
                <div class='col-md-2'>
                    $ref_line
                    $type_line
                    $testin
                </div>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label for='text'>Text:</label>
                        <textarea id='text' name='text' class='form-control'>$text</textarea>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-md-4'>
                </div>
            </div>
        </form>
    HTML;

    return $nana;
}
// ---
$title       = $_POST['title'] ?? '';
$text        = $_POST['text'] ?? '';
$refs_expend = ($_POST['refs_expend'] ?? '') == 1 ? true : false;
$test        = $_POST['test'] ?? '';
$fixref      = $_POST['fixref'] ?? '';
// ---
function get_result($title, $text, $refs_expend)
{

    $new_text = "";
    $page_line = "";
    // ---
    if (!empty($title)) {
        $title = trim($title);
        // ---
        $articleurl = "https://mdwiki.org/w/index.php?title=$title";
        // ---
        $page_line = "page: <a target='_blank' href='$articleurl'>$title</a>";
        // ---
        test_print("enwiki/index.php; title: $title");
        // ---
        $tr_type = ($_POST['tr_type'] ?? '') == 1 ? 'all' : 'lead';
        // ---
        $wholearticle = $tr_type == 'all' ? true : false;
        //---
        $newtext = startTranslatePhp($title, $tr_type, true, $expend_refs = $refs_expend);
        //---
        // trim
        $newtext = trim($newtext);
        //---
        $new_text = htmlentities($newtext);
        //---
    } elseif (!empty($text)) {
        $page_line = "work on text.";
        // ---
        $newtext = TranslatePhpEditText($text, $expend_refs = $refs_expend);
        //---
        $new_text = htmlentities($newtext);
    }
    //---
    $done = "";
    if (!empty($new_text)) {
        $done = "done";
        $new_text = <<<HTML
            new text:
            <textarea class="form-control" cols="20" rows="10">$new_text</textarea>
        HTML;
    }
    //---
    $result = <<<HTML
        <div class='card'>
            <div class="card-header aligncenter" style="font-weight:bold;">
                <h3>
                    $page_line
                </h3>
            </div>
            <div class='card-body'>
                $new_text
            </div>
            <div class='card-footer'>
                $done.
            </div>
        </div>
    HTML;

    return $result;
}
// ---
$nana = make_form($text);
// ---
$result = get_result($title, $text, $refs_expend);
// ---
echo <<<HTML
    <div class='container'>
        <div class='card'>
            <div class='card-header' style='font-weight: bold;'>
                test get text from mdwiki.org and do changes on it before send it to en.wikipedia.org: (test:$test)
            </div>
            <div class='card-body'>
                <div class="container">
                    $nana
                </div>
            </div>
        </div>
        <hr />
        $result
    </div>
    <br>
HTML;

include_once __DIR__ . '/../foter.php';
