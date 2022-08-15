<?PHP
//==========================
require('tables.php');
include_once('functions.php');
require('langcode.php');
//==========================
function strstartswith ( $haystack, $needle ) {
  return strpos( $haystack , $needle ) === 0;
};
//==========================
// views (target, countall, count2021, count2022, count2023, lang)
$qua_views2 = "select p.target, p.user, 
v.lang, v.countall, v.count2021, v.count2022, v.count2023
, p.pupdate
from pages p,views v
where p.lang = v.lang
and p.target = v.target
;
";
$views_quarye = quary2($qua_views2);
//---
$all_views_by_lang = array();
$all_views_by_lang['all'] = array();
$all_views_by_lang['2021'] = array();
$all_views_by_lang['2022'] = array();
$all_views_by_lang['2023'] = array();
//---
$Views_by_users = array();
$Views_by_users['all'] = array();
$Views_by_users['2021'] = array();
$Views_by_users['2022'] = array();
$Views_by_users['2023'] = array();
//---
$views_by_target = array();
// $views_by_user = array();
$global_views = array();
$global_views['all'] = 0;
$global_views['2021'] = 0;
$global_views['2022'] = 0;
$global_views['2023'] = 0;
//----
foreach ( $views_quarye AS $Key => $tablea ) {
    //--------------------
    $llang  = $tablea['lang'];
    $use    = $tablea['user'];
    $allviews = $tablea['countall'];
    $tat    = $tablea['target'];
    //---------------------
    // $lang_count = isset($sql_Languages_tab[$llang]) ? $sql_Languages_tab[$llang] : "";
    // if ($lang_count == '') { $sql_Languages_tab[$llang] = array(); };
    //---------------------
    $lang_in = isset($all_views_by_lang['all'][$llang]) ? $all_views_by_lang['all'][$llang] : "";
    if ($lang_in == '') { $all_views_by_lang['all'][$llang] = 0; };
    $all_views_by_lang['all'][$llang] = $all_views_by_lang['all'][$llang] + $allviews;
    //--------------------
    $user_in = isset($Views_by_users['all'][$use]) ? $Views_by_users['all'][$use] : "";
    if ($user_in == '') { $Views_by_users['all'][$use] = 0; };
    //--------------------
    $Views_by_users['all'][$use] = $Views_by_users['all'][$use] + $allviews;
    //--------------------
    $views_by_target[$tat] = $allviews;
    //--------------------
    $global_views['all'] = $global_views['all'] + $allviews;
    //--------------------
    // 2021
    //--------------------
    $pupdate   = $tablea['pupdate'];
    $count2021   = $tablea['count2021'];
    $count2022   = $tablea['count2022'];
    $count2023   = $tablea['count2023'];
    //------------------
    $ttt2021 = strstartswith( $pupdate , '2021' );
    $ttt2022 = strstartswith( $pupdate , '2022' );
    $ttt2023 = strstartswith( $pupdate , '2023' );
    //--------------------
    //==================
    $the_year = '';
    $coco = 0;
    //==================
    if ($ttt2021) {
        $the_year = '2021';
        $coco = $count2021;
    } elseif ($ttt2022) {
        $the_year = '2022';
        $coco = $count2022;
    } elseif ($ttt2023) {
        $the_year = '2023';
        $coco = $count2023;
    };
    //--------------------
    $lang_in = isset($all_views_by_lang[$the_year][$llang]) ? $all_views_by_lang[$the_year][$llang] : "";
    if ($lang_in == '') { $all_views_by_lang[$the_year][$llang] = 0; };
    $all_views_by_lang[$the_year][$llang] = $all_views_by_lang[$the_year][$llang] + $allviews;
    //--------------------
    // views (target, countall, count2021, count2022, count2023, lang)
    $user_in3 = isset($Views_by_users[$the_year][$use]) ? $Views_by_users[$the_year][$use] : "";
    if ($user_in3 == '') { $Views_by_users[$the_year][$use] = 0; };
    //--------------------
    $Views_by_users[$the_year][$use] = $Views_by_users[$the_year][$use] + $coco;
    //--------------------
    $global_views[$the_year] = $global_views[$the_year] + $coco;
    //--------------------
    //--------------------
    };
//==========================
$sql_t = 'select user,lang,word,target,pupdate
from pages
#where target != "";
';
//--------------------
$sql_u = quary2($sql_t);
//--------------------
$user_process_tab = array();
//--------------------
$Words_total = array();
$Words_total['all'] = 0;
$Words_total['2021'] = 0;
$Words_total['2022'] = 0;
$Words_total['2023'] = 0;

$Articles_number = array();
$Articles_number['all'] = 0;
$Articles_number['2021'] = 0;
$Articles_number['2022'] = 0;
$Articles_number['2023'] = 0;
//--------------------
$sql_users_tab = array();
$sql_users_tab['all'] = array();
$sql_users_tab['2021'] = array();
$sql_users_tab['2022'] = array();
$sql_users_tab['2023'] = array();
//--------------------
$Users_word_table = array();
$Users_word_table['all'] = array();
$Users_word_table['2021'] = array();
$Users_word_table['2022'] = array();
$Users_word_table['2023'] = array();
//--------------------
$sql_Languages_tab = array();
$sql_Languages_tab['all'] = array();
$sql_Languages_tab['2021'] = array();
$sql_Languages_tab['2022'] = array();
$sql_Languages_tab['2023'] = array();
//--------------------
foreach ( $sql_u AS $id => $table ) {
    //--------------------
    $target = $table['target'];
    $user = $table['user'];
    $word = $table['word'];
    // $word = number_format( $table['word']);
    $lang = $table['lang'];
    //--------------------
    if ($target != '') {
        // completd translations
        $lang_in = isset($sql_Languages_tab['all'][$lang]) ? $sql_Languages_tab['all'][$lang] : "";
        if ($lang_in == '') { $sql_Languages_tab['all'][$lang] = 0; };
        //--------------------
        $sql_Languages_tab['all'][$lang] = $sql_Languages_tab['all'][$lang] + 1 ;
        //--------------------
        $user_in = isset($sql_users_tab['all'][$user]) ? $sql_users_tab['all'][$user] : "";
        if ($user_in == '') {
            $sql_users_tab['all'][$user] = 0;
            $Users_word_table['all'][$user] = 0;
            };
        //--------------------
        $sql_users_tab['all'][$user] = $sql_users_tab['all'][$user] + 1 ;
        $Users_word_table['all'][$user] = $Users_word_table['all'][$user] + $word ;
        //--------------------
        $Articles_number['all'] = $Articles_number['all'] + 1 ;
        $Words_total['all'] = $Words_total['all'] + $word ;
        //--------------------
        $pupdate = $table['pupdate'];
        $ttt2021 = strstartswith( $pupdate , '2021' );
        $ttt2022 = strstartswith( $pupdate , '2022' );
        $ttt2023 = strstartswith( $pupdate , '2023' );
        // 2021
        //==================
        $the_year = '';
        //==================
        if ($ttt2021) {
            $the_year = '2021';
        } elseif ($ttt2022) {
            $the_year = '2022';
        } elseif ($ttt2023) {
            $the_year = '2023';
        };
        //--------------------
        $lang_in2 = isset($sql_Languages_tab[$the_year][$lang]) ? $sql_Languages_tab[$the_year][$lang] : "";
        if ($lang_in2 == '') { $sql_Languages_tab[$the_year][$lang] = 0; };
        //--------------------
        $sql_Languages_tab[$the_year][$lang] = $sql_Languages_tab[$the_year][$lang] + 1 ;
        //--------------------
        //--------------------
        $user_in2 = isset($sql_users_tab[$the_year][$user]) ? $sql_users_tab[$the_year][$user] : "";
        if ($user_in2 == '') {
            $sql_users_tab[$the_year][$user] = 0;
            $Users_word_table[$the_year][$user] = 0;
        };
        //--------------------
        $sql_users_tab[$the_year][$user] = $sql_users_tab[$the_year][$user] + 1 ;
        $Users_word_table[$the_year][$user] = $Users_word_table[$the_year][$user] + $word ;
        //------------------------------------
        $Articles_number[$the_year] = $Articles_number[$the_year] + 1 ;
        $Words_total[$the_year] = $Words_total[$the_year] + $word ;
        //--------------------
    } else {
        // Translations in process
        $user2_in = isset($user_process_tab[$user]) ? $user_process_tab[$user] : "";
        if ($user2_in == '') {
            $user_process_tab[$user] = 0;
        };
        //--------------------
        $user_process_tab[$user] = $user_process_tab[$user] + 1;
        //--------------------
        
    }; 
};
//--------------------
function ma_Numbers_table($uu) {
    //--------------------
    global $sql_users_tab,$Articles_number,$Words_total,$sql_Languages_tab,$global_views;
    //--------------------
    $Numbers_table = '
    <table class="sortable table table-striped alignleft">
    <tr>
    <th onclick="sortTable(0)" class="spannowrap">Type</th>
    <th onclick="sortTable(1)">Number</th>
    </tr>
    ';
    //--------------------
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
    //--------------------
    return $Numbers_table;
};
//==========================
function Make_users_table($Viewstable,$Users_tables,$Words_tables) {
    //--------------------
    //---------
    $text = '
    <table class="sortable table table-striped alignleft" style="width:97%;">
    <tr>
    <th onclick="sortTable(0)" class="spannowrap">User</th>
    <th onclick="sortTable(1)">Number</th>
    <th onclick="sortTable(1)">Words</th>
    <th onclick="sortTable(1)">Pageviews</th>
    </tr>
    ';
    //--------------------
    arsort($Users_tables);
    //--------------------
    foreach ( $Users_tables as $user => $number ) {
        //if ($user != 'test') { 
            //--------------------
            $views = $Viewstable[$user];
            $words = $Words_tables[$user];
            //--------------------
            $use = rawurlEncode($user);
            $use = str_replace ( '+' , '_' , $use );
            //--------------------
            $text .= '
        <tr>
            <td><a href="users.php?user=' . $use . '">' . $user . '</a></td>
            <td>' . $number . '</td>
            <td>' . number_format($words) . '</td>
            <td>' . number_format($views) . '</td>
        </tr>
            ';
        //};
    };
    //--------------------
    $text .= '
</table>';
    //--------------------
    return $text;
}
//==========================
function Make_lang_table($lang_array,$views_array) {
    //--------------------
    global $code_to_lang;
    //--------------------
    arsort($lang_array);
    //--------------------
    $text = '
    <table class="sortable table table-striped alignleft">
    <tr>
    <th onclick="sortTable(0)" class="spannowrap">Language</th>
    <th onclick="sortTable(2)">Count</th>
    <th onclick="sortTable(2)">Pageviews</th>
    </tr>
    ';
    //--------------------
    foreach ( $lang_array as $langcode => $comp ) {
        //--------------------
        # Get the Articles numbers
        //--------------------
        if ( $comp > 0 ) {
            //--------------------
            $langname  = isset($code_to_lang[$langcode]) ? $code_to_lang[$langcode] : $langcode;
            //--------------------
            $view = $views_array[$langcode];
            //--------------------
            //--------------------
            if ($comp != 0) {
                $text .= '
            <tr>
                <td><a href="langs.php?langcode=' . $langcode . '">' . $langname . '</a></td>
                <td>' . $comp . '</td>
                <td>' . number_format($view) . '</td>
                ';
            //--------------------
            if ( $_SERVER['SERVER_NAME'] == 'localhost' ) { 
                $text .= '<td><a target="_blank" href="https://' . $langcode . '.wikipedia.org/wiki/Category:Translated_from_MDWiki">(cat)</a></td>';
            };
            //==========================
            //--------------------
                $text .= '
            </tr>';
            };
            //--------------------
        };
    };
    //--------------------
    $text .= '
</table>';
    //--------------------
    return $text;
}
//==========================
function Make_Pinding_table() {
    //--------------------
    global $user_process_tab;
    //--------------------
    $text = '
    <table class="sortable table table-striped alignleft">
    <tr>
    <th onclick="sortTable(0)" class="spannowrap">User</th>
    <th onclick="sortTable(1)">Number</th>';
    
    //--------------------
    if ($_REQUEST['test'] != '') {
        $text .= '<th onclick="sortTable(1)">comp</th><th onclick="sortTable(1)">all</th>';
    };
    //--------------------
    $text .= '
    </tr>
    ';
    //--------------------
    arsort($user_process_tab);
    //--------------------
    foreach ( $user_process_tab AS $user => $pinde ) {
        if ($user != 'test' && $user != '') {
            //--------------------
            $use = rawurlEncode($user);
            $use = str_replace ( '+' , '_' , $use );
            //--------------------
            if ($pinde > 0 ) {
                //--------------------
                $text .= '
                <tr>
                    <td><a href="users.php?user=' . $use . '">' . $user . '</a></td>
                    <td>' . $pinde . '</td>';
                $text .= '
                </tr>
                ';
            };
        };
    };
    //--------------------
    $text .= '
</table>';
    //--------------------
    return $text;
}
//==========================
?>