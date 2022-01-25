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
//--------------------
$dirr = '/mnt/nfs/labstore-secondary-tools-project/mdwiki/public_html/Translation_Dashboard';
//--------------------
if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'localhost') { 
	$dirr = '/master/mdwiki/public_html/Translation_Dashboard';
};
//--------------------
//--------------------
$viewsfile   = file_get_contents("Tables/views.json");
$views_table = json_decode ( $viewsfile ) ;
//--------------------
$wordfile = file_get_contents("Tables/words.json");
$Words_table = json_decode ( $wordfile ) ;
//-------------------- 
$allwordfile = file_get_contents("Tables/allwords.json");
$All_Words_table = json_decode ( $allwordfile ) ;
//--------------------

$allreffile = file_get_contents("Tables/all_refcount.json");
$All_Refs_table = json_decode ( $allreffile ) ;
//--------------------
// $Lead_Refs_table,$All_Refs_table

$lead_ref_file = file_get_contents("Tables/lead_refcount.json");
$Lead_Refs_table = json_decode ( $lead_ref_file ) ;
//--------------------

$md_en_text = file_get_contents("Tables/medwiki_to_enwiki.json");
$medwiki_to_enwiki = json_decode ( $md_en_text ) ;
//--------------------
$assef = file_get_contents("Tables/assessments.json");
$Assessments_table = json_decode ( $assef ) ;
//--------------------
//==========================
/*
$allwords = $_REQUEST['print'];
$printe = $_REQUEST['print'];
//==========================
if ($allwords  != '' ) {
    echo $All_Words_table->{$allwords};
};
//==========================
if ($printe  == 'allwords' ) {
    echo json_encode($All_Words_table);
} elseif ($printe  == 'words' ) {
    echo json_encode($Words_table);
};  
*/
//==========================
// $lal = file_get_contents("Tables/leader.csv");
// $nany = '[' . $lal . '{}]';
// $leadere_csv = json_decode ( $nany ) ;
// $jsonfileaa = file_get_contents("Tables/leader.json");
//==========================
?>