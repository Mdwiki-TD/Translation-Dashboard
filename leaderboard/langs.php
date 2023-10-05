<?PHP
//---
require('lead_help.php');
require 'camps.php';
//---
$test = $_REQUEST['test'] ?? '';
$mainlang = $_REQUEST['langcode'];
$mainlang = rawurldecode( str_replace ( '_' , ' ' , $mainlang ) );
//---
$langname = $code_to_lang[$mainlang] ?? $mainlang;
//---
$man = $langname;
//---
if ( $_SERVER['SERVER_NAME'] == 'localhost' || $test != '' ) { 
    $man .= ' <a target="_blank" href="http://' . $mainlang . '.wikipedia.org/wiki/Category:Translated_from_MDWiki">(cat)</a>';
};
//---
// views (target, countall, count2021, count2022, count2023, lang)
$qua_views = <<<SQL
select 
    #p.title, p.user, p.date, p.word, p.lang, p.cat, p.pupdate, 
    p.target, v.countall

    from pages p, views v
    where p.lang = '$mainlang'
    and p.lang = v.lang
    and p.target = v.target
    ;
SQL;
$views_quary = execute_query($qua_views);
//---
$dd = array();
$dd_Pending = array();
$table_of_views = array();
//---
foreach ( $views_quary AS $Key => $t ) $table_of_views[$t['target']] = $t['countall'];
//---
foreach ( execute_query("select * from pages where lang = '$mainlang'") AS $yhu => $Taab ) {
	//---
	$dat1 = $Taab['pupdate'] ?? '';
	$dat2 = $Taab['date'] ?? '';
	$dat = ($dat1 != '') ? $dat1 : $dat2;
    //---
	$urt = '';
	if ($dat != '') {
		$urt = str_replace('-','',$dat) . ':';
	};
	$kry = $urt . $Taab['lang'] . ':' . $Taab['title'] ;
	//---
	if ( $Taab['target'] != '' ) {
		$dd[$kry] = $Taab;
	} else {
		$dd_Pending[$kry] = $Taab;
	};
	//---
};
//---
krsort($dd);
//---
$tat = make_table_lead($dd, $tab_type='translations', $views_table = $table_of_views, $page_type='langs', $user='', $lang=$mainlang);
//---
$table1 = $tat['table1'];
$table2 = $tat['table2'];
//---
echo "
<div class='row content'>
    <div class='col-md-4'>$table1</div>
    <div class='col-md-4'><h2 class='text-center'>$man</h2></div>
    <div class='col-md-4'></div>
</div>
<div class='card'>
    <div class='card-body' style='padding:5px 0px 5px 5px;'>
    $table2
    </div>
</div>";
//---
krsort($dd_Pending);
//---
$table_pnd = make_table_lead($dd_Pending, $tab_type='pending', $page_type='langs', $user='', $lang=$mainlang);
//---
$tab_pnd = $table_pnd['table2'];
//---
print "
<br>
<div class='card'>
	<div class='card-body' style='padding:5px 0px 5px 5px;'>
        <h2 class='text-center'>Translations in process</h2>
        $tab_pnd
	</div>
</div>";
//---
?>