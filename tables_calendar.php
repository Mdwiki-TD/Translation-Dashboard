<?PHP
//---
require('tables.php');
include_once('functions.php');
require('langcode.php');
//---
$sql_ttt = 'select user,lang,word,target,pupdate,CONCAT(left(pupdate,7)) as month
from pages
where target != "";
';
//---
$sql_cu = quary2($sql_ttt);
//---
$Total_words_cal = array();
$Count_articles_cal = array();
//---
$Count_users_cal = array();
//---
$Users_words_cal = array();
//---
$Count_langs_cal = array();
//---
foreach ( $sql_cu AS $id => $table ) {
    //---
    $target = $table['target'];
    $use = $table['user'];
    $word = $table['word'];
    $lang = $table['lang'];
    //---
	$month   = $table['month'];
    //---
    if ($target != '' and $month != '') {
        // completd translations
        if (isset($Count_langs_cal[$month]) == '') $Count_langs_cal[$month] = array();
        if (isset($Count_langs_cal[$month][$lang]) == '') $Count_langs_cal[$month][$lang] = 0;
        //---
        $Count_langs_cal[$month][$lang] = $Count_langs_cal[$month][$lang] + 1 ;
		//---
		//---
		if (isset($Count_articles_cal[$month]) == '') $Count_articles_cal[$month] = 0;
		$Count_articles_cal[$month] = $Count_articles_cal[$month] + 1 ;
		//---
		if (isset($Total_words_cal[$month]) == '') $Total_words_cal[$month] = 0;
		$Total_words_cal[$month] = $Total_words_cal[$month] + $word ;
		//---
		//---
		//---
		// add month to the array
		if (isset($Count_users_cal[$month]) == '') $Count_users_cal[$month] = array();
		//---
		// add user to month array
		if (isset($Count_users_cal[$month][$use]) == '') $Count_users_cal[$month][$use] = 0;
		//---
		$Count_users_cal[$month][$use] = $Count_users_cal[$month][$use] + 1;
		//---
		// add month to the array
		if (isset($Users_words_cal[$month]) == '') $Users_words_cal[$month] = array();
		//---
		// add user to month array
		if (isset($Users_words_cal[$month][$use]) == '') $Users_words_cal[$month][$use] = 0;
		//---
		$Users_words_cal[$month][$use] = $Users_words_cal[$month][$use] + $word;
		//---
        //---
		//---
    };
};
//---
function Make_Numbers_table($Users_n,$artic_n,$words_n,$langs_n,$views) {
	//---
	$Numbers_table = '
	<table class="sortable table table-striped alignleft"> <!-- scrollbody -->
	<tr>
	<th onclick="sortTable(0)" class="spannowrap">Type</th>
	<th onclick="sortTable(1)">Number</th>
	</tr>
	';
	//---
	$Numbers_table .= '<tr><td><b>Users</b></td><td>' .     $Users_n		. '</td></tr>';
	$Numbers_table .= '<tr><td><b>Articles</b></td><td>' .  $artic_n		. '</td></tr>';
	$Numbers_table .= '<tr><td><b>Words</b></td><td>' .     $words_n		. '</td></tr>';
	$Numbers_table .= '<tr><td><b>Languages</b></td><td>' . $langs_n		. '</td></tr>';
	$Numbers_table .= '<tr><td><b>Pageviews</b></td><td>' . $views		. '</td></tr>';
	$Numbers_table .= '</table>';
	//---
	return $Numbers_table;
};
//---
function Make_users_table_cal($Viewstable,$Users_tables,$Words_tables) {
    //---
    //---
    $text = '
    <table class="sortable table table-striped alignleft" style="width:95%;">
    <tr>
    <th onclick="sortTable(0)" class="spannowrap">User</th>
    <th onclick="sortTable(1)">Number</th>
    <th onclick="sortTable(1)">Words</th>
    <th onclick="sortTable(1)">Pageviews</th>
	';
	$text .= '
	</tr>';
    //---
    arsort($Users_tables);
    //---
    foreach ( $Users_tables as $user => $number ) {
        //---
        $views = isset($Viewstable[$user]) ? $Viewstable[$user] : 0;
        $words = isset($Words_tables[$user]) ? $Words_tables[$user] : 0;
        //---
        $use = rawurlEncode($user);
        $use = str_replace ( '+' , '_' , $use );
        //---
        $text .= '
        <tr>
        <td><a href="users.php?user=' . $use . '">' . $user . '</a></td>
        <td>' . $number . '</td>
        <td>' . number_format($words) . '</td>
        <td>' . number_format($views) . '</td></tr>';
    };
    //---
    $text .= '
</table>';
    //---
    return $text;
}
//---
function Make_lang_table_cal($langs_counts,$langs_views) {
    //---
    global $code_to_lang;
    //---
    arsort($langs_counts);
    //---
    $text = '
    <table class="sortable table table-striped alignleft"> <!-- scrollbody -->
    <tr>
    <th onclick="sortTable(0)" class="spannowrap">Language</th>
    <th onclick="sortTable(2)">Count</th>';
	$text .= ' <th onclick="sortTable(2)">Pageviews</th>';
    $text .= '</tr>';
    //---
    foreach ( $langs_counts as $langcode => $comp ) {
        //---
        # Get the Articles numbers
        //---
        if ( $comp > 0 ) {
            //---
            $langname  = isset($code_to_lang[$langcode]) ? $code_to_lang[$langcode] : $langcode;
            //---
            $view = isset($langs_views[$langcode]) ? $langs_views[$langcode] : 0;
            //---
            //---
            if ($comp != 0) {
                $text .= '
            <tr>
                <td><a href="langs.php?langcode=' . $langcode . '">' . $langname . '</a></td>
                <td>' . $comp . '</td>';
                $text .= '<td>' . number_format($view) . '</td>';
                
            //---
            if ( $_SERVER['SERVER_NAME'] == 'localhost' ) { 
                $text .= '<td><a target="_blank" href="https://' . $langcode . '.wikipedia.org/wiki/Category:Translated_from_MDWiki">(cat)</a></td>';
            };
            //---
            //---
                $text .= '
            </tr>';
            };
            //---
        };
    };
    //---
    $text .= '
</table>';
    //---
    return $text;
}
//---
if ($_REQUEST['test'] != '' ) echo "<br>load " . str_replace ( __dir__ , '' , __file__ ) . " true.";
//---
?>