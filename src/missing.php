<?PHP

include_once __DIR__ . '/include_all.php';
include_once __DIR__ . '/header.php';

use function SQLorAPI\Funcs\exists_statics_by_category;

if (isset($_REQUEST['test']) || isset($_COOKIE['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};


$text = "";
$num = 0;

$data = exists_statics_by_category("RTT");

$length = 0;

foreach ($data as $row) {
    // { "language_code": "ar", "autonym": "العربية", "language_name": "Arabic", "available_title_count": 3132, "missing_title_count": 4, "total": 3136 }
    $langcode = $row['language_code'] ?? '';
    $autonym  = $row['autonym'] ?? '';
    $langname = $row['language_name'] ?? "";

    $missing = $row['missing_title_count'] ?? "0";
    $exists  = $row['available_title_count'] ?? "0";

    if ($length == 0) $length = $row['total'] ?? 0;

    $num += 1;

    if (empty($autonym)) $autonym = "! autonym";
    if (empty($langname)) $langname = "! langname";

    $missing_numb = number_format($missing);

    $text .= <<<HTML
        <tr>
            <th data-content="#">
                $num
            </th>
            <td data-content="Language name">
                <a target="_blank" href="https://$langcode.wikipedia.org">$langname</a>
            </td>
            <td data-content="Language code">
                $langcode
            </td>
            <td data-content="Autonym">
                $autonym
            </td>
            <td data-content="Exists Articles">$exists</td>
            <td data-content="Missing Articles" data-search="$missing_numb">
                <a href="index.php?cat=RTT&depth=1&doit=Do+it&code=$langcode&type=lead">$missing_numb</a>
            </td>
        </tr>
        HTML;
};

echo <<<HTML
        <div align=center>
        <h4>Top languages by missing Articles</h4>
        <h5>Number of pages in Category:RTT : $length</h5>
    </div>
    <div class='card'>
        <div class='card-body' style='padding:5px 0px 5px 5px;'>
            <table class="table table-striped compact soro table-mobile-responsive table_100 table_text_left">
                <thead>
                    <tr>
                        <th class="spannowrap">#</th>
                        <th class="spannowrap">Language Name</th>
                        <th class="spannowrap">Code</th>
                        <th class="spannowrap">Autonym</th>
                        <th>Exists Articles</th>
                        <th>Missing Articles</th>
                    </tr>
                </thead>
                <tbody>
                    $text
                </tbody>
            </table>
        </div>
    </div>
</div>
HTML;

include_once __DIR__ . '/footer.php';
