<?PHP
//---
if (isset($_REQUEST['test'])) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
};
//---
$Assessments_fff = array();
$Assessments_fff['Top'] = 1;
$Assessments_fff['High'] = 2;
$Assessments_fff['Mid'] = 3;
$Assessments_fff['Low'] = 4;
$Assessments_fff['Unknown'] = 5;
$Assessments_fff[''] = 5;
//---
//---
$dirr = '/data/project/mdwiki/public_html/Translation_Dashboard';
//---
if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'localhost') { 
	$dirr = '/mdwiki/public_html/Translation_Dashboard';
};
//---
$pv_file = file_get_contents("Tables/enwiki_pageviews.json");
$enwiki_pageviews_table = json_decode( $pv_file, true) ;
//---
$lc_file = file_get_contents("Tables/lang_code_to_en.json");
$lang_code_to_en = json_decode( $lc_file, true) ;
//---
$wordfile = file_get_contents("Tables/words.json");
$Words_table = json_decode( $wordfile, true) ;
//--- 
$allwordfile = file_get_contents("Tables/allwords.json");
$All_Words_table = json_decode( $allwordfile, true ) ;
//---
$allreffile = file_get_contents("Tables/all_refcount.json");
$All_Refs_table = json_decode( $allreffile, true ) ;
//---
// $Lead_Refs_table,$All_Refs_table
$lead_ref_file = file_get_contents("Tables/lead_refcount.json");
$Lead_Refs_table = json_decode( $lead_ref_file, true) ;
//---
$md_en_text = file_get_contents("Tables/medwiki_to_enwiki.json");
$medwiki_to_enwiki = json_decode( $md_en_text, true) ;
//---
$assef = file_get_contents("Tables/assessments.json");
$Assessments_table = json_decode( $assef, true) ;
//---
/*
$qids_table = json_decode( file_get_contents("Tables/mdwiki_to_qid.json"), true) ;
$qids_2 = json_decode( file_get_contents("Tables/other_qids.json"), true) ;
//---
foreach( $qids_2 as $ta => $q ) {
    //---
    if ($ta != '' && $q != '' ) {
        $qids_table[$ta] = $q;
    };
};
*/
//---

//---
?>