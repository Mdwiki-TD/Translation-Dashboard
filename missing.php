<?PHP
//---
require('header.php');
require('langcode.php');
//---
//---
//---
if ($_GET['test'] != '') {

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
};
//---
$missingfile = file_get_contents("Tables/missing.json");
//print $wordsjson;
$MIS = json_decode( $missingfile, true) ; //{'all' : len(listenew), 'date' : Day_History, 'langs' : {} }
//---
//$lenth = file_get_contents("len.csv"); 
$lenth = $MIS['all']; 
$lenth2 = number_format($lenth); 
//---
//$date = '15-05-2021';
$date = $MIS['date']; 
//---
//---
$Table = array(); 
$langs = $MIS['langs']; 
//---
foreach ( $langs as $code => $tabe ) {
    //$tabe = { 'missing' leeen :  , 'exists' : len( table[langs] ) };
    $aaa = $tabe['missing'];
    //$aaa = number_format( $aaa );
    $Table[$code] = $aaa;
};
//---
arsort( $Table );
//---
//---
$text = '
<table class="table table-striped compact soro">
    <thead>
        <tr>
        <th class="spannowrap">#</th>
        <th class="spannowrap">Language code</th>
        <th class="spannowrap">Language name</th>
        <th>Exists Articles</th>
        <th>Missing Articles</th>
        </tr>
    </thead>
    <tbody>
';
//---
$num = 0;
//---
foreach ( $Table as $langcode => $missing ) {
    $num = $num + 1;
    $langname = isset($code_to_lang[$langcode]) ? $code_to_lang[$langcode] : $langcode;
    $langname = str_replace ( "($langcode) " , '' , $langname ) ;
    //---
    $exists = bcsub($lenth , $missing);
    // $exists = $langs->{$langcode}->{'exists'}; 
    $exists = isset($langs[$langcode]['exists']) ? $langs[$langcode]['exists'] : ''; 
    //---
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
//---
$text .= '
    </tbody>
    </table>';
//---
print "
        <div align=center>
        <h4>Top languages by missing Articles ($date)</h4>
        <h5>Number of pages in Category:RTT : $lenth</h5>
        </div>
";
//---
print "<div class='card'>
  <div class='card-body' style='padding:5px 0px 5px 5px;'>
  $text
  </div>
  </div>"
  ;
//---
//---
//---
//---
print "
        </div>";
//---

//---
?>
<?php
//---
require('foter.php');
//---
?>