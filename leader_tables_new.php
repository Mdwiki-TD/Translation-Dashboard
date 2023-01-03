<?PHP
//---
require('tables.php');
include_once('functions.php');
require('langcode.php');
//---
function strstartswith ( $haystack, $needle ) {
  return strpos( $haystack , $needle ) === 0;
};
//---
// views (target, countall, count2021, count2022, count2023, lang)
$qua_views2 = "select p.target, p.user, p.cat, 
v.lang, v.countall, v.count2021, v.count2022, v.count2023, 
p.pupdate, year(p.pupdate) as pup_y
from pages p,views v
where p.lang = v.lang
and p.target = v.target
;
";
$views_quarye = quary2($qua_views2);
//---
$all_views_by_lang = array();
$all_views_by_lang['all'] = array();
//---
$Views_by_users = array();
$Views_by_users['all'] = array();
//---
$views_by_target = array();
// $views_by_user = array();
$global_views = array();
$global_views['all'] = 0;
//---
foreach ( $views_quarye AS $Key => $tablea ) {
    //---
    $cat    = $tablea['cat'];
    $llang  = $tablea['lang'];
    $use    = $tablea['user'];
    $allviews = $tablea['countall'];
    $tat    = $tablea['target'];
    //---
    $the_year    = $tablea['pup_y'];
    //---
    $pupdate     = $tablea['pupdate'];
    //---
    $counts = array();
    $counts['2021'] = $tablea['count2021'];
    $counts['2022'] = $tablea['count2022'];
    $counts['2023'] = $tablea['count2023'];
    //---
    //---
    $coco = isset($counts[$the_year]) ? $counts[$the_year] : 0;
    //---
    //---
    // if (isset($sql_Languages_tab[$llang]) == '') $sql_Languages_tab[$llang] = array();
    //---
    if (isset($all_views_by_lang['all'][$llang]) == '') $all_views_by_lang['all'][$llang] = 0;
    $all_views_by_lang['all'][$llang] = $all_views_by_lang['all'][$llang] + $allviews;
    //---
    if (isset($all_views_by_lang[$the_year]) == '') $all_views_by_lang[$the_year] = array();
    //---
    if (isset($all_views_by_lang[$the_year][$llang]) == '') $all_views_by_lang[$the_year][$llang] = 0;
    $all_views_by_lang[$the_year][$llang] = $all_views_by_lang[$the_year][$llang] + $allviews;
    //---
    if (!isset($all_views_by_lang[$cat])) $all_views_by_lang[$cat] = array();
    //---
    if (!isset($all_views_by_lang[$cat][$llang])) $all_views_by_lang[$cat][$llang] = 0;
    //---
    $all_views_by_lang[$cat][$llang] += $allviews;
    //---
    
    
    //---
    if (isset($Views_by_users['all'][$use]) == '') { $Views_by_users['all'][$use] = 0; };
    //---
    $Views_by_users['all'][$use] = $Views_by_users['all'][$use] + $allviews;
    //---
    if (isset($Views_by_users[$the_year]) == '') $Views_by_users[$the_year] = array();
    // views (target, countall, count2021, count2022, count2023, lang)
    if (isset($Views_by_users[$the_year][$use]) == '') $Views_by_users[$the_year][$use] = 0;
    $Views_by_users[$the_year][$use] = $Views_by_users[$the_year][$use] + $coco;
    //---
    if (!isset($Views_by_users[$cat])) $Views_by_users[$cat] = array();
    if (!isset($Views_by_users[$cat][$use])) $Views_by_users[$cat][$use] = 0;
    //---
    $Views_by_users[$cat][$use] = $Views_by_users[$cat][$use] + $allviews;
    //---
    
    
    //---
    $views_by_target[$tat] = $allviews;
    //---
    $global_views['all'] = $global_views['all'] + $allviews;
    //---
    if (isset($global_views[$the_year]) == '') $global_views[$the_year] = 0;
    $global_views[$the_year] = $global_views[$the_year] + $coco;
    //---
    
    
    //---
    if (!isset($global_views[$cat])) $global_views[$cat] = 0;
    //---
    $global_views[$cat] += $allviews;
    //---
    };
//---
$sql_t = 'select user, lang, word, target, pupdate, year(pupdate) as pup_y, cat
from pages
#where target != "";
';
//---
$sql_u = quary2($sql_t);
//---
$user_process_tab = array();
//---
$Words_total = array();
$Words_total['all'] = 0;

$Articles_number = array();
$Articles_number['all'] = 0;
//---
$sql_users_tab = array();
$sql_users_tab['all'] = array();
//---
$Users_word_table = array();
$Users_word_table['all'] = array();
//---
$sql_Languages_tab = array();
$sql_Languages_tab['all'] = array();
//---
foreach ( $sql_u AS $id => $table ) {
    //---
    $target = $table['target'];
    $user = $table['user'];
    $word = $table['word'];
    // $word = number_format( $table['word']);
    $lang = $table['lang'];
    $cat  = $table['cat'];
    //---
    if ($target != '') {
        //---
        $pupdate  = $table['pupdate'];
        $the_year = $table['pup_y'];
        //---
        // completd translations
        if (!isset($sql_Languages_tab['all'][$lang]))       $sql_Languages_tab['all'][$lang] = 0;
        //---
        $sql_Languages_tab['all'][$lang] =                  $sql_Languages_tab['all'][$lang] + 1 ;
        //---
        if (!isset($sql_Languages_tab[$the_year]))          $sql_Languages_tab[$the_year] = array();
        if (!isset($sql_Languages_tab[$the_year][$lang]))   $sql_Languages_tab[$the_year][$lang] = 0;
        $sql_Languages_tab[$the_year][$lang] =              $sql_Languages_tab[$the_year][$lang] + 1 ;
        //---
        if (!isset($sql_Languages_tab[$cat]))          $sql_Languages_tab[$cat] = array();
        if (!isset($sql_Languages_tab[$cat][$lang]))   $sql_Languages_tab[$cat][$lang] = 0;
        $sql_Languages_tab[$cat][$lang] =              $sql_Languages_tab[$cat][$lang] + 1 ;
        //---

        
        //---
        if (!isset($sql_users_tab['all'][$user])) $sql_users_tab['all'][$user] = 0;
        $sql_users_tab['all'][$user] = $sql_users_tab['all'][$user] + 1 ;
        //---
        if (!isset($sql_users_tab[$the_year])) $sql_users_tab[$the_year] = array();
        if (!isset($sql_users_tab[$the_year][$user])) $sql_users_tab[$the_year][$user] = 0;
        $sql_users_tab[$the_year][$user] = $sql_users_tab[$the_year][$user] + 1 ;
        //---
        if (!isset($sql_users_tab[$cat])) $sql_users_tab[$cat] = array();
        if (!isset($sql_users_tab[$cat][$user])) $sql_users_tab[$cat][$user] = 0;
        $sql_users_tab[$cat][$user] = $sql_users_tab[$cat][$user] + 1 ;
        //---


        //---
        if (!isset($Users_word_table['all'][$user])) $Users_word_table['all'][$user] = 0;
        $Users_word_table['all'][$user] = $Users_word_table['all'][$user] + $word ;
        //---
        if (!isset($Users_word_table[$the_year]))           $Users_word_table[$the_year] = array();
        if (!isset($Users_word_table[$the_year][$user]))    $Users_word_table[$the_year][$user] = 0;
        $Users_word_table[$the_year][$user] =               $Users_word_table[$the_year][$user] + $word ;
        //---
        if (!isset($Users_word_table[$cat]))           $Users_word_table[$cat] = array();
        if (!isset($Users_word_table[$cat][$user]))    $Users_word_table[$cat][$user] = 0;
        $Users_word_table[$cat][$user] =               $Users_word_table[$cat][$user] + $word ;
        //---


        //---
        $Articles_number['all'] = $Articles_number['all'] + 1 ;
        //---
        if (!isset($Articles_number[$the_year])) $Articles_number[$the_year] = 0;
        $Articles_number[$the_year] = $Articles_number[$the_year] + 1 ;
        //---
        if (!isset($Articles_number[$cat])) $Articles_number[$cat] = 0;
        $Articles_number[$cat] = $Articles_number[$cat] + 1 ;
        //---


        //---
        $Words_total['all'] = $Words_total['all'] + $word ;
        //---
        if (!isset($Words_total[$the_year])) $Words_total[$the_year] = 0;
        $Words_total[$the_year] = $Words_total[$the_year] + $word ;
        //---
        if (!isset($Words_total[$cat])) $Words_total[$cat] = 0;
        $Words_total[$cat] = $Words_total[$cat] + $word ;
        //---
    } else {
        // Translations in process
        if (isset($user_process_tab[$user]) == '') $user_process_tab[$user] = 0;
        //---
        $user_process_tab[$user] = $user_process_tab[$user] + 1;
        //---
    }; 
};
//---
function ma_Numbers_table($uu) {
    //---
    global $sql_users_tab,$Articles_number,$Words_total,$sql_Languages_tab,$global_views;
    //---
    $Numbers_table = '
    <table class="sortable table table-striped alignleft"> <!-- scrollbody -->
    <tr>
    <th onclick="sortTable(0)" class="spannowrap">Type</th>
    <th onclick="sortTable(1)">Number</th>
    </tr>
    ';
    //---
    $Numbers_table .= '<tr><td><b>Users</b></td><td>' .     count($sql_users_tab[$uu])           . '</td></tr>
    ';
    $Numbers_table .= '<tr><td><b>Articles</b></td><td>' .  number_format($Articles_number[$uu]) . '</td></tr>
    ';
    $Numbers_table .= '<tr><td><b>Words</b></td><td>' .     number_format($Words_total[$uu])     . '</td></tr>
    ';
    $Numbers_table .= '<tr><td><b>Languages</b></td><td>' . count($sql_Languages_tab[$uu])       . '</td></tr>
    ';
    $Numbers_table .= '<tr><td><b>Pageviews</b></td><td>' . number_format($global_views[$uu])    . '</td></tr>
    ';
    $Numbers_table .= '</table>';
    //---
    return $Numbers_table;
};
//---
function Make_users_table($Viewstable,$Users_tables,$Words_tables) {
    //---
    //---
    $text = '
    <table class="sortable table table-striped alignleft" style="width:97%;"> <!-- scrollbody -->
    <thead>
        <tr>
            <th onclick="sortTable(0)" class="spannowrap">#</th>
            <th onclick="sortTable(1)" class="spannowrap">User</th>
            <th onclick="sortTable(2)">Number</th>
            <th onclick="sortTable(3)">Words</th>
            <th onclick="sortTable(4)">Pageviews</th>
        </tr>
    </thead>
    <tbody>
    ';
    //---
    arsort($Users_tables);
    //---
    $numb = 0;
    //---
    foreach ( $Users_tables as $user => $number ) {
        //if ($user != 'test') { 
            //---
            $numb += 1;
            //---
            $views = isset($Viewstable[$user]) ? $Viewstable[$user] : 0;
            $words = isset($Words_tables[$user]) ? $Words_tables[$user] : 0;
            //---
            $use = rawurlEncode($user);
            $use = str_replace ( '+' , '_' , $use );
            //---
            $text .= '
        <tr>
            <td>' . $numb . '</td>
            <td><a href="users.php?user=' . $use . '">' . $user . '</a></td>
            <td>' . $number . '</td>
            <td>' . number_format($words) . '</td>
            <td>' . number_format($views) . '</td>
        </tr>
            ';
        //};
    };
    //---
    $text .= '
    </tbody>
    <tfoot></tfoot>
    </table>';
    //---
    return $text;
}
//---
function Make_lang_table($lang_array,$views_array) {
    //---
    global $code_to_lang;
    //---
    arsort($lang_array);
    //---
    $text = '
    <table class="sortable table table-striped alignleft">  <!-- scrollbody -->
    <tr>
    <th onclick="sortTable(0)" class="spannowrap">Language</th>
    <th onclick="sortTable(2)">Count</th>
    <th onclick="sortTable(2)">Pageviews</th>
    ';
    //---
    if ( $_SERVER['SERVER_NAME'] == 'localhost' ) $text .= '<th>cat</th>';
    //---
    $text .=
    '</tr>
    ';
    //---
    foreach ( $lang_array as $langcode => $comp ) {
        //---
        # Get the Articles numbers
        //---
        if ( $comp > 0 ) {
            //---
            $langname  = isset($code_to_lang[$langcode]) ? $code_to_lang[$langcode] : $langcode;
            //---
            $view = isset($views_array[$langcode]) ? $views_array[$langcode] : 0;
            //---
            //---
            if ($comp != 0) {
                $text .= '
            <tr>
                <td><a href="langs.php?langcode=' . $langcode . '">' . $langname . '</a></td>
                <td>' . $comp . '</td>
                <td>' . number_format($view) . '</td>
                ';
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