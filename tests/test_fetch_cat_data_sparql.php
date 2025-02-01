<?PHP
//---

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once __DIR__ . '/../header.php';
include_once __DIR__ . '/../results/include.php';
//---
use function Results\FetchCatDataSparql\get_cat_exists_and_missing;
//---
$Category = $_GET['Category'] ?? '';
$lang = $_GET['lang'] ?? '';
$depth = $_GET['depth'] ?? '';
$use_cache = $_GET['use_cache'] ?? '';
//---
if (!empty($Category)) {
    $time_start = microtime(true);
    // ---
    $items = get_cat_exists_and_missing($Category, "", $depth, $lang, $use_cache = $use_cache);
    // ---
    $execution_time = (microtime(true) - $time_start);
    echo "<br> >>>>> Total Execution Time: " . $execution_time . " Seconds<br>";
    // ---
    echo "len_of_exists: " . $items['len_of_exists'] . "<br>";
    echo "len of missing: " . count($items["missing"]) . "<br>";
    // ---
    echo "<pre> missing:";
    print(json_encode($items["missing"], JSON_PRETTY_PRINT));
    echo "</pre>";
    // ---
} else {
    echo <<<HTML
        <div class='container'>
        <div class='card'>
            <div class='card-header'>
            </div>
            <div class='card-body mb-0'>
            <div class='mainindex'>
                <form action="test_fetch_cat_data_sparql.php" method="GET">
                    <input id="test" name="test11" value="1" hidden/>
                    <div class="container">
                        <div class="row">
                            <div class="col-md-7">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Category</span>
                                    </div>
                                    <input class="form-control" type="text" id="Category" name="Category" value="World Health Organization essential medicines" required>
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Depth</span>
                                    </div>
                                    <input class="form-control" type="number" id="depth" name="depth" value="0" required>
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Lang</span>
                                    </div>
                                    <input class="form-control" type="text" id="lang" name="lang" value="ar" required>
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">use_cache</span>
                                    </div>
                                    <input class="form-control" type="text" id="use_cache" name="use_cache" value="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <h4 class="aligncenter">
                                    <input class="btn btn-outline-primary" type="submit" value="Start">
                                </h4>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            </div>
        </div>
        </div>
        <br>
    HTML;
}
//---
include_once __DIR__ . '/../footer.php';
