<?PHP
//--------------------
require('header.php');
require('langcode.php');
//--------------------
print '';
//==========================
// print '<div class="col-md-10 col-md-offset-1" align=left >';
print '<div style="margin-right:8%;margin-left:8%;boxSizing:border-box;" align=left>';
//==========================
//print '<h1 class="text-center">Leaderboard</h1>';
print '<div class="text-center clearfix leaderboard" >';
//==========================
//--------------------
$missingfile = file_get_contents("cash/missing.json");
//print $wordsjson;
$MIS = json_decode ( $missingfile ) ; //{'all' : len(listenew), 'date' : Day_History, 'langs' : {} }
//==========================
//$lenth = file_get_contents("len.csv"); 
$lenth = $MIS->{'all'}; 
$lenth2 = number_format($lenth); 
//==========================
//$date = '15-05-2021';
$date = $MIS->{'date'}; 
//==========================
//==========================
/*
$Table = array();
foreach ( $code_to_lang as $code => $name ) {
    $tex = file_get_contents("cash/" . $code . "_exists.csv");
    if ($tex != '') {
        $lines = explode("\n",$tex);
        $len = count($lines);
    } else {
        $len = '0';
    };
    //print var_dump($len) + '<br>';
    $nana = bcsub($lenth , $len);
    $Table[$code] = $nana;
    
};*/
//--------------------
$Table = array(); 
$langs = $MIS->{'langs'}; 
//--------------------
foreach ( $langs as $code => $tabe ) {
    //$tabe = { 'missing' leeen :  , 'exists' : len( table[langs] ) };
    
    $aaa = $tabe->{'missing'};
    //$aaa = number_format( $aaa );
    $Table[$code] = $aaa;
};
//--------------------
arsort( $Table );
//--------------------
//==========================
print "<h3>Top languages by missing Articles ($date)</h3>";
print "<h4>Number of pages in Category:RTT : $lenth</h4>";
//--------------------
function Make_lang_table( $tabe ) {
    global $code_to_lang;
    global $langs;
    global $lenth;
    $text = '
    <table class="sortable table table-striped alignleft">
    <tr>
    <th onclick="sortTable(0)" class="text-nowrap">#</th>
    <th onclick="sortTable(0)" class="text-nowrap">Language code</th>
    <th onclick="sortTable(0)" class="text-nowrap">Language name</th>
    <th onclick="sortTable(1)">Number of exists Articles</th>
    <th onclick="sortTable(1)">Number of missing Articles</th>
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
        $exists = $langs->{$langcode}->{'exists'}; 
        //--------------------
        $text .= '
    <tr>
        <td>' . $num . '</td>
        <td>' . $langcode . '</td>
        <td><a target="" href="https://' . $langcode . '.wikipedia.org">' . $langname . '</a></td>
        <td>' . $exists . '</td>
        <td><a target="" href="index.php?cat=RTT&depth=1&proj1=wiki&format=html&doit=Do+it&code='. $langcode .'">' . number_format($missing) . '</a></td>
    </tr>
        ';
    };
    //--------------------
    $text .= '
</table>';
    //--------------------
    return $text;
}
//==========================
//--------------------
print Make_lang_table( $Table );
//--------------------
?>
<?PHP
//--------------------
print "</div>";
//--------------------

print "
</main>
<!-- Footer -->
</body>
</html>
</div>"
//--------------------

?>