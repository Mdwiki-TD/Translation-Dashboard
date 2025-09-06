<?PHP
//---
include_once __DIR__ . '/include_all.php';
include_once __DIR__ . '/header.php';
//---

use Tables\Langs\LangsTables;
use Tables\Main\MainTables;
use function Tables\TablesDir\open_td_Tables_file;
use function SQLorAPI\TopData\get_td_or_sql_top_langs;

//---
if (isset($_REQUEST['test']) || isset($_COOKIE['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};
//---
$MIS = open_td_Tables_file("/jsons/missing.json"); //{'all' : len(listenew), 'date' : Day_History, 'langs' : {} }
//---
//$length = file_get_contents("len.csv");
$length = $MIS['all'] ?? 0;
//---
//$date = '15-05-2021';
$date = $MIS['date'] ?? "";
//---
$Table = [];
$langs = $MIS['langs'] ?? [];
//---
foreach ($langs as $code => $tabe) {
    //$tabe = { 'missing' leeen :  , 'exists' : len( table[langs] ) };
    $aaa = $tabe['missing'] ?? 0;
    //$aaa = number_format( $aaa );
    $Table[$code] = $aaa;
};
//---
// foreach (MainTables::$x_Langs_table as $_ => $lang_tab) {
//     $lang_code = $lang_tab['code'] ?? "";
//     if (!isset($Table[$lang_code])) {
//         $Table[$lang_code] = 0;
//     }
// }
//---
$lang_codes = array_map(function ($lang_tab) {
    return $lang_tab['code'] ?? "";
}, MainTables::$x_Langs_table);

$Table += array_fill_keys(array_filter($lang_codes), $length);
//---
arsort($Table);
//---
$text = <<<HTML
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

HTML;
//---
$num = 0;
//---
$tab_done = [];
//---
$translated_data = get_td_or_sql_top_langs("", "", "");
//---
$translated_data = array_column($translated_data, 'targets', 'lang');
//---
foreach ($Table as $langcode2 => $missing) {
    //---
    $langcode = $langcode2;
    //---
    $langcode = LangsTables::$L_change_codes[$langcode] ?? $langcode;
    //---
    // skip langcode in LangsTables::$L_skip_codes
    // if (array_intersect([$langcode, $langcode2], LangsTables::$L_skip_codes)) {
    if (!empty(array_intersect([$langcode, $langcode2], LangsTables::$L_skip_codes))) {
        continue;
    };
    //---
    $translated = $translated_data[$langcode] ?? 0;
    $translated = ($translated > 0) ? "<a href='leaderboard.php?get=langs&langcode=$langcode'>$translated</a>" : 0;
    //---
    if (isset($tab_done[$langcode])) continue;
    $tab_done[$langcode] = true;
    //---
    $lang_info = MainTables::$x_Langs_table[$langcode] ?? MainTables::$x_Langs_table[$langcode2] ?? [];
    //---
    $num += 1;
    //---
    $autonym = $lang_info['autonym'] ?? '';
    // ---
    if (empty($autonym)) $autonym = "! autonym";
    // ---
    $langname = $lang_info['name'] ?? "";
    //---
    if (empty($langname)) $langname = "! langname";
    // ---
    $exists_1 = bcsub($length, $missing);
    $exists = $langs[$langcode]['exists'] ?? '';
    #---
    if (empty($exists)) $exists = $langs[$langcode2]['exists'] ?? $exists_1;
    //---
    $numb = number_format($missing);
    //---
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
            <td data-content="Translated Articles">$translated</td>
            <td data-content="Exists Articles">$exists</td>
            <td data-content="Missing Articles">
                <a target="" href="index.php?cat=RTT&depth=1&doit=Do+it&code=$langcode&type=lead">$numb</a>
            </td>
        </tr>
        HTML;
};
//---
$text .= <<<HTML
    </tbody>
    </table>
    HTML;
//---
echo <<<HTML
        <div align=center>
        <h4>Top languages by missing Articles ($date)</h4>
        <h5>Number of pages in Category:RTT : $length</h5>
    </div>
    <div class='card'>
        <div class='card-body' style='padding:5px 0px 5px 5px;'>
        $text
        </div>
    </div>
</div>
HTML;
//---
include_once __DIR__ . '/footer.php';
//---
