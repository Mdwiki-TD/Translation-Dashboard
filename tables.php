<?PHP
//--------------------
/*

*/
//--------------------
// require('langcode.php');
//--------------------
$Assessments_fff = array();
$Assessments_fff['Top'] = 1;
$Assessments_fff['High'] = 2;
$Assessments_fff['Mid'] = 3;
$Assessments_fff['Low'] = 4;
$Assessments_fff['Unknown'] = 5;
$Assessments_fff[''] = 5;
//--------------------
// $ts_pw = posix_getpwuid(posix_getuid());
//--------------------
$viewsfile   = file_get_contents("Tables/views.json");
$views_table = json_decode ( $viewsfile ) ;
//--------------------
// $wordfile = file_get_contents($ts_pw['dir'] ."/public_html/Translation_Dashboard/Tables/words.json");
$wordfile = file_get_contents("Tables/words.json");
$Words_table = json_decode ( $wordfile ) ;
//--------------------
// $assef = file_get_contents($ts_pw['dir'] . "/public_html/Translation_Dashboard/Tables/assessments.json");
$assef = file_get_contents("Tables/assessments.json");
$Assessments_table = json_decode ( $assef ) ;
//--------------------
//==========================
// $lal = file_get_contents("Tables/leader.csv");
// $nany = '[' . $lal . '{}]';
// $leadere_csv = json_decode ( $nany ) ;
// $jsonfileaa = file_get_contents("Tables/leader.json");
//==========================
?>