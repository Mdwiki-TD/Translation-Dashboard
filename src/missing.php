<?PHP

include_once __DIR__ . '/include_all.php';
include_once __DIR__ . '/header.php';

use Tables\Langs\LangsTables;
use function Tables\TablesDir\open_td_Tables_file;
use function SQLorAPI\TopData\get_td_or_sql_top_langs;
use function SQLorAPI\GetDataTab\get_td_or_sql_langs;

if (isset($_REQUEST['test']) || isset($_COOKIE['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};

$MIS = open_td_Tables_file("/jsons/missing.json"); //{'all' : len(listenew), 'date' : Day_History, 'langs' : {} }

//$length = file_get_contents("len.csv");
$length = $MIS['all'] ?? 0;

//$date = '15-05-2021';
$date = $MIS['date'] ?? "";

$langs_missing_data = $MIS['langs'] ?? [];

$langs_table = get_td_or_sql_langs();

$text = "";
$num = 0;
$tab_done = [];

$translated_data = get_td_or_sql_top_langs("", "", "");
$translated_data = array_column($translated_data, 'targets', 'lang');

foreach ($langs_table as $_ => $lang_info) {
    $langcode = $lang_info['code'] ?? '';
    $langcode = LangsTables::$L_change_codes[$langcode] ?? $langcode;

    if (in_array($langcode, LangsTables::$L_skip_codes)) {
        continue;
    };

    $translated = $translated_data[$langcode] ?? 0;
    $translated_url = ($translated > 0) ? "<a href='leaderboard.php?get=langs&langcode=$langcode'>$translated</a>" : 0;

    if (isset($tab_done[$langcode])) continue;
    $tab_done[$langcode] = true;

    $num += 1;

    $redirects = $lang_info['redirects'] ?? [];
    $autonym = $lang_info['autonym'] ?? '';

    if (empty($autonym)) $autonym = "! autonym";

    $langname = $lang_info['name'] ?? "";

    if (empty($langname)) $langname = "! langname";

    $exists = $langs_missing_data[$langcode]['exists'] ?? 0;
    if ($exists === 0) {
        foreach ($redirects as $redirect) {
            $exists = $langs_missing_data[$redirect]['exists'] ?? 0;
            if ($exists > 0) break;
        }
    }
    $missing = (int)$length - (int)$exists;
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
            <td data-content="Translated Articles" data-search="$translated">$translated_url</td>
            <td data-content="Exists Articles">$exists</td>
            <td data-content="Missing Articles" data-search="$missing_numb">
                <a href="index.php?cat=RTT&depth=1&doit=Do+it&code=$langcode&type=lead">$missing_numb</a>
            </td>
        </tr>
        HTML;
};

echo <<<HTML
        <div align=center>
        <h4>Top languages by missing Articles ($date)</h4>
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
                        <th>Translated Articles</th>
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
