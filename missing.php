<?PHP
//--------------------
require('header.php');
require('langcode.php');
//--------------------
//==========================
//--------------------
$missingfile = file_get_contents("cash/missing.json");
//print $wordsjson;
$MIS = json_decode( $missingfile, true) ; //{'all' : len(listenew), 'date' : Day_History, 'langs' : {} }
//==========================
//$lenth = file_get_contents("len.csv"); 
$lenth = $MIS['all']; 
$lenth2 = number_format($lenth); 
//==========================
//$date = '15-05-2021';
$date = $MIS['date']; 
//==========================
//==========================
$Table = array(); 
$langs = $MIS['langs']; 
//--------------------
foreach ( $langs as $code => $tabe ) {
    //$tabe = { 'missing' leeen :  , 'exists' : len( table[langs] ) };
    $aaa = $tabe['missing'];
    //$aaa = number_format( $aaa );
    $Table[$code] = $aaa;
};
//--------------------
arsort( $Table );
//--------------------
//==========================
$tab_start = '
<div class="table-responsive">
<table class="sortable table table-striped alignleft">
';

$tab_end = '
</table>
</div>';
//==========================
function Make_lang_table( $tabe ) {
    global $tab_end, $tab_start;
    global $code_to_lang, $langs, $lenth;
    $text = $tab_start . '
    <tr>
    <th onclick="sortTable(0)" class="spannowrap">#</th>
    <th onclick="sortTable(1)" class="spannowrap">Language code</th>
    <th onclick="sortTable(2)" class="spannowrap">Language name</th>
    <th onclick="sortTable(3)">Exists Articles</th>
    <th onclick="sortTable(4)">Missing Articles</th>
    </tr>
    ';
    //--------------------
    $num = 0;
    //--------------------
    //<!-- <td><a target="" href="langs.php?langcode=' . $langcode . '">' . $numb . '</a></td> -->
    //--------------------
    foreach ( $tabe as $langcode => $missing ) {
        $num = $num + 1;
        $langname = isset($code_to_lang[$langcode]) ? $code_to_lang[$langcode] : $langcode;
        $langname = str_replace ( "($langcode) " , '' , $langname ) ;
        //--------------------
        $exists = bcsub($lenth , $missing);
        // $exists = $langs->{$langcode}->{'exists'}; 
        $exists = $langs[$langcode]['exists']; 
        //--------------------
        $text .= '
    <tr>
        <td>' . $num . '</td>
        <td>' . $langcode . '</td>
        <td><a target="" href="https://' . $langcode . '.wikipedia.org">' . $langname . '</a></td>
        <td>' . $exists . '</td>
        <td><a target="" href="index.php?cat=RTT&depth=1&doit=Do+it&code='. $langcode .'&type=all">' . number_format($missing) . '</a></td>
    </tr>
        ';
    };
    //--------------------
    $text .= $tab_end;
    //--------------------
    return $text;
}
//==========================
print "
        <div align=center>
        <h3>Top languages by missing Articles ($date)</h3>
        <h4>Number of pages in Category:RTT : $lenth</h4>
        </div>
        <div class='clearfix leaderboard'>
";
//==========================
//--------------------
print Make_lang_table( $Table );
//--------------------
//--------------------
print "
        </div>";
//--------------------
require('foter.php');
//--------------------
?>