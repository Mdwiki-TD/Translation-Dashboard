<?PHP
//---
require 'lead_help.php';
require 'camps.php';
//---
$test = $_REQUEST['test'] ?? '';
$mainlang = $_REQUEST['langcode'];
$mainlang = rawurldecode( str_replace ( '_' , ' ' , $mainlang ) );
//---
$year_y = $_REQUEST['year'] ?? 'All';
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
$pages_qua = <<<SQL
	select * from pages where lang = '$mainlang'
SQL;
//---
if ($year_y != 'All') {
	$pages_qua .= " and YEAR(date) = '$year_y'";
};
//---
foreach ( execute_query($pages_qua) AS $yhu => $Taab ) {
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
function make_filter_form($mainlang) {
    //---
    global $lang_y, $year_y;
    //---
    $d33 = <<<HTML
    <div class="input-group">
        <span class="input-group-text">%s</span>
        %s
    </div>
    HTML;
    //---
	$years_q = <<<SQL
		SELECT CONCAT(left(pupdate, 4)) AS year
		FROM pages
		WHERE lang = '$mainlang'
		GROUP BY
			left(pupdate, 4)
		SQL;
	
	$years = [];

	foreach (execute_query($years_q) as $key => $table) {
		$years[] = $table['year'];
	}
	$years = array_unique($years);
    //---
    $y3 = makeDropdown($years, $year_y, 'year', 'All');
    $yearDropdown = sprintf($d33, 'Year', $y3);
    //---
    return <<<HTML
        <form method="get" action="leaderboard.php">
            <input type="hidden" name="langcode" value="$mainlang" />
            <div class='container g-3'>
                <div class='row content'>
                    <div class="col-md-4">
                        $yearDropdown
                    </div>
                    <div class="aligncenter col-md-6">
                        <input class='btn btn-primary' type='submit' name='start' value='Filter' />
                    </div>
                </div>
            </div>
        </form>
        HTML;
}
//---
krsort($dd);
//---
$tat = make_table_lead($dd, $tab_type='translations', $views_table = $table_of_views, $page_type='langs', $user='', $lang=$mainlang);
//---
$table1 = $tat['table1'];
$table2 = $tat['table2'];
//---
$filter_form = make_filter_form($mainlang);
//---
echo <<<HTML
	<div class='row content'>
		<div class='col-md-3'>$table1</div>
		<div class='col-md-3'><h2 class='text-center'>$man</h2></div>
		<div class='col-md-6'>$filter_form</div>
	</div>
	<div class='card'>
		<div class='card-body' style='padding:5px 0px 5px 5px;'>
			$table2
		</div>
	</div>
HTML;
//---
krsort($dd_Pending);
//---
$table_pnd = make_table_lead($dd_Pending, $tab_type='pending', $page_type='langs', $user='', $lang=$mainlang);
//---
$tab_pnd = $table_pnd['table2'];
//---
echo <<<HTML
	<br>
	<div class='card'>
		<div class='card-body' style='padding:5px 0px 5px 5px;'>
			<h2 class='text-center'>Translations in process</h2>
			$tab_pnd
		</div>
	</div>
HTML;
//---
?>