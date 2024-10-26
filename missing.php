<?PHP
//---
include_once __DIR__ . '/header.php';
include_once __DIR__ . '/Tables/langcode.php';
include_once __DIR__ . '/Tables/tables.php';

echo '<script>$("#missing").addClass("active");</script>';
//---
if (isset($_REQUEST['test'])) {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};
//---
$missingfile = file_get_contents("Tables/jsons/missing.json");
//print $wordsjson;
$MIS = json_decode($missingfile, true); //{'all' : len(listenew), 'date' : Day_History, 'langs' : {} }
//---
//$lenth = file_get_contents("len.csv");
$lenth = $MIS['all'] ?? "";
$lenth2 = number_format($lenth);
//---
//$date = '15-05-2021';
$date = $MIS['date'] ?? "";
//---
$Table = array();
$langs = $MIS['langs'] ?? [];
//---
foreach ($langs as $code => $tabe) {
    //$tabe = { 'missing' leeen :  , 'exists' : len( table[langs] ) };
    $aaa = $tabe['missing'] ?? 0;
    //$aaa = number_format( $aaa );
    $Table[$code] = $aaa;
};
//---
foreach ($Langs_table as $_ => $lang_tab) {
    $lang_code = $lang_tab['code'] ?? "";
    if (!isset($Table[$lang_code])) {
        $Table[$lang_code] = 0;
    }
}
//---
arsort($Table);
//---
$text = <<<HTML
<table class="table table-striped compact soro table-mobile-responsive table-mobile-sided">
    <thead>
        <tr>
        <th class="spannowrap">#</th>
        <th class="spannowrap">Language code</th>
        <th class="spannowrap">Language name</th>
        <th class="spannowrap">Autonym</th>
        <th>Exists Articles</th>
        <th>Missing Articles</th>
        </tr>
    </thead>
    <tbody>

HTML;
//---
$num = 0;
//---
foreach ($Table as $langcode2 => $missing) {
    //---
    $langcode = $langcode2;
    //---
    $langcode = $change_codes[$langcode] ?? $langcode;
    //---
    // skip langcode in $skip_codes
    // if (in_array($langcode, $skip_codes) || in_array($langcode2, $skip_codes)) {
    if (array_intersect([$langcode, $langcode2], $skip_codes)) {
        continue;
    };
    //---
    $lang_info = $Langs_table[$langcode] ?? $Langs_table[$langcode2] ?? [];
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
    $exists_1 = bcsub($lenth, $missing);
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
            <td data-content="Language code">
                <a target="_blank" href="leaderboard.php?langcode=$langcode">$langcode</a>
            </td>
            <td data-content="Language name">
                <a target="_blank" href="https://$langcode.wikipedia.org">$langname</a>
            </td>
            <td data-content="Autonym">
                $autonym
            </td>
            <td data-content="Exists Articles">
                $exists
            </td>
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
        <h5>Number of pages in Category:RTT : $lenth</h5>
    </div>
    <div class='card'>
        <div class='card-body' style='padding:5px 0px 5px 5px;'>
        $text
        </div>
    </div>
</div>
HTML;
//---
include_once __DIR__ . '/foter.php';
//---
