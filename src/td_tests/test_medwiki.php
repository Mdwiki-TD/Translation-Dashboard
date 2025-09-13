<?PHP
//---

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once __DIR__ . '/../include_all.php';
include_once __DIR__ . '/../header.php';
//---
use function Results\GetCats\get_mdwiki_cat_members;
use function Results\TrLink\make_translate_link_medwiki; // make_translate_link_medwiki($title, $cod, $cat, $camp, $tra_type)
use function TD\Render\TestPrint\test_print;
//---
$Category = $_GET['Category'] ?? 'World Health Organization essential medicines';
$depth = $_GET['depth'] ?? 0;
$lang = $_GET['lang'] ?? "ar";
$tr_type = $_GET['tr_type'] ?? "lead";
//---
$members = get_mdwiki_cat_members($Category, $depth, true);
//---
test_print("members size:" . count($members));
//---
$rows = "";
//---
foreach ($members as $member) {
    $link = make_translate_link_medwiki($member, $lang, $Category, "", $tr_type);
    $rows .= <<<HTML
        <div class="list-group-item">
            <a href="$link" target="_blank">$member</a>
        </div>
    HTML;
}
//---
echo <<<HTML
    <div class='container'>
        <div class='card'>
            <div class='card-header'>
            </div>
            <div class='card-body mb-0'>
                <div class='mainindex'>
                    <form action="test_medwiki.php" method="GET">
                        <div class="container">
                            <div class="row">
                                <div class="col">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Category</span>
                                        </div>
                                        <input class="form-control" type="text" id="Category" name="Category"
                                            value="$Category" required>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Lang</span>
                                        </div>
                                        <input class="form-control" type="text" id="lang" name="lang"
                                            value="$lang" required>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Translation Type</span>
                                        </div>
                                        <input class="form-control" type="text" id="tr_type" name="tr_type"
                                            value="$tr_type" required>
                                    </div>
                                </div>
                                <div class="col">
                                    <h4 class="aligncenter">
                                        <input class="btn btn-outline-primary" type="submit" value="Start">
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <hr>
                <div class="list-group list-group-numbered">
                    $rows
                </div>
            </div>
        </div>
    </div>
HTML;
//---
include_once __DIR__ . '/../footer.php';
