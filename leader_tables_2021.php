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
//---
$Views_by_users = array();
$Views_by_users['all'] = array();
$Views_by_users['2021'] = array();
$Views_by_users['2022'] = array();
$Views_by_users['2023'] = array();
//---
$views_by_target = array();
// $views_by_user = array();
$global_views = 0;
//----
foreach ( $views_quarye AS $Key => $tablea ) {
    //--------------------
    $llang  = $tablea['lang'];
    $use    = $tablea['user'];
    $Counte = $tablea['countall'];
    $tat    = $tablea['target'];
    //---------------------
    // $lang_count = isset($sql_Languages_tab[$llang]) ? $sql_Languages_tab[$llang] : "";
    // if ($lang_count == '') { $sql_Languages_tab[$llang] = array(); };
    //---------------------
    $lang_in = isset($all_views_by_lang[$llang]) ? $all_views_by_lang[$llang] : "";
    if ($lang_in == '') { $all_views_by_lang[$llang] = 0; };
    $all_views_by_lang[$llang] = $all_views_by_lang[$llang] + $Counte;
    //--------------------
    $user_in = isset($Views_by_users['all'][$use]) ? $Views_by_users['all'][$use] : "";
    if ($user_in == '') { $Views_by_users['all'][$use] = 0; };
    //--------------------
    $Views_by_users['all'][$use] = $Views_by_users['all'][$use] + $Counte;
    //--------------------
    $views_by_target[$tat] = $Counte;
    //--------------------
    $global_views = $global_views + $Counte;
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
    // views (target, countall, count2021, count2022, count2023, lang)
    if ($ttt2021) { 
        $user_in2 = isset($Views_by_users['2021'][$use]) ? $Views_by_users['2021'][$use] : "";
        if ($user_in2 == '') { $Views_by_users['2021'][$use] = 0; };
        $Views_by_users['2021'][$use] = $Views_by_users['2021'][$use] + $count2021;
        //--------------------
    } elseif ($ttt2022) { 
        $user_in3 = isset($Views_by_users['2022'][$use]) ? $Views_by_users['2022'][$use] : "";
        if ($user_in3 == '') { $Views_by_users['2022'][$use] = 0; };
        $Views_by_users['2022'][$use] = $Views_by_users['2022'][$use] + $count2022;
        //--------------------
    } elseif ($ttt2023) { 
        $user_in3 = isset($Views_by_users['2023'][$use]) ? $Views_by_users['2023'][$use] : "";
        if ($user_in3 == '') { $Views_by_users['2023'][$use] = 0; };
        //--------------------
        $Views_by_users['2023'][$use] = $Views_by_users['2023'][$use] + $count2023;
    };
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
$Words_total = 0;
$Articles_number = 0;
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
//--------------------
$sql_Languages_tab = array();
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
        $lang_in = isset($sql_Languages_tab[$lang]) ? $sql_Languages_tab[$lang] : "";
        if ($lang_in == '') { $sql_Languages_tab[$lang] = 0; };
        //--------------------
        $sql_Languages_tab[$lang] = $sql_Languages_tab[$lang] + 1 ;
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
        $Articles_number = $Articles_number + 1 ;
        $Words_total = $Words_total + $word ;
        //--------------------
        $pupdate = $table['pupdate'];
        $ttt2021 = strstartswith( $pupdate , '2021' );
        $ttt2022 = strstartswith( $pupdate , '2022' );
        $ttt2023 = strstartswith( $pupdate , '2023' );
        // 2021
        if ($ttt2021) {
            $user_in2 = isset($sql_users_tab['2021'][$user]) ? $sql_users_tab['2021'][$user] : "";
            if ($user_in2 == '') {
                $sql_users_tab['2021'][$user] = 0;
                $Users_word_table['2021'][$user] = 0;
            };
            //--------------------
            $sql_users_tab['2021'][$user] = $sql_users_tab['2021'][$user] + 1 ;
            $Users_word_table['2021'][$user] = $Users_word_table['2021'][$user] + $word ;
		//--------------------
		// 2022
        } elseif ($ttt2022) {
            $user_in3 = isset($sql_users_tab['2022'][$user]) ? $sql_users_tab['2022'][$user] : "";
            if ($user_in3 == '') {
                $sql_users_tab['2022'][$user] = 0;
                $Users_word_table['2022'][$user] = 0;
            };
            //--------------------
            $sql_users_tab['2022'][$user] = $sql_users_tab['2022'][$user] + 1 ;
            $Users_word_table['2022'][$user] = $Users_word_table['2022'][$user] + $word ;
		//--------------------
		// 2023
        } elseif ($ttt2023) {
            $user_in3 = isset($sql_users_tab['2023'][$user]) ? $sql_users_tab['2023'][$user] : "";
            if ($user_in3 == '') {
                $sql_users_tab['2023'][$user] = 0;
                $Users_word_table['2023'][$user] = 0;
            };
            //--------------------
            $sql_users_tab['2023'][$user] = $sql_users_tab['2023'][$user] + 1 ;
            $Users_word_table['2023'][$user] = $Users_word_table['2023'][$user] + $word ;
        };
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
$Numbers_table = '
<h3>Numbers</h3>
<table class="sortable table table-striped alignleft">
<tr>
<th onclick="sortTable(0)" class="text-nowrap">Type</th>
<th onclick="sortTable(1)">Number</th>
</tr>
';
//--------------------
$Numbers_table .= '<tr><td><b>Users</b></td><td>' .     count($sql_users_tab['all'])           . '</td></tr>';
$Numbers_table .= '<tr><td><b>Articles</b></td><td>' .  number_format($Articles_number) . '</td></tr>';
$Numbers_table .= '<tr><td><b>Words</b></td><td>' .     number_format($Words_total)     . '</td></tr>';
$Numbers_table .= '<tr><td><b>Languages</b></td><td>' . count($sql_Languages_tab)       . '</td></tr>';
$Numbers_table .= '<tr><td><b>Pageviews</b></td><td>' . number_format($global_views)    . '</td></tr>';
$Numbers_table .= '</table>';
//--------------------
//==========================
function Make_users_table($Viewstable,$Users_tables,$Words_tables) {
    //--------------------
    //---------
    $text = '
    <table class="sortable table table-striped alignleft" style="width:100%;">
    <tr>
    <th onclick="sortTable(0)" class="text-nowrap">User</th>
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
function Make_lang_table() {
    //--------------------
    global $all_views_by_lang;
    global $code_to_lang;
    //--------------------
    global $sql_Languages_tab;
    arsort($sql_Languages_tab);
    //--------------------
    $text = '
    <h3>Top languages by number of Articles</h3>
    <table class="sortable table table-striped alignleft">
    <tr>
    <th onclick="sortTable(0)" class="text-nowrap">Language</th>
    <th onclick="sortTable(2)">Count</th>
    <th onclick="sortTable(2)">Pageviews</th>
    </tr>
    ';
    //--------------------
    foreach ( $sql_Languages_tab as $langcode => $comp ) {
        //--------------------
        # Get the Articles numbers
        //--------------------
        if ( $comp > 0 ) {
            //--------------------
            $langname  = isset($code_to_lang[$langcode]) ? $code_to_lang[$langcode] : $langcode;
            //--------------------
            $view = $all_views_by_lang[$langcode];
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
<h3>Number of translations</h3>
    <table class="sortable table table-striped alignleft">
    <tr>
    <th onclick="sortTable(0)" class="text-nowrap">User</th>
    <th onclick="sortTable(1)">Number</th>';
    
    //--------------------
    if ($_REQUEST['test'] != '') $text .= '<th onclick="sortTable(1)">comp</th><th onclick="sortTable(1)">all</th>';
    //--------------------
    $text .= '
    </tr>
    ';
    //--------------------
    arsort($user_process_tab);
    //--------------------
    foreach ( $user_process_tab AS $user => $pinde ) {
        if ($user != 'test') { 
            //--------------------
            $use = rawurlEncode($user);
            $use = str_replace ( '+' , '_' , $use );
            //--------------------
            if ($pinde > 0 ) {
                //--------------------
                $text .= '<tr>
                <td><a href="users.php?user=' . $use . '">' . $user . '</a></td>
                <td>' . $pinde . '</td>';
                $text .= '</tr>';
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
function GET_2021_USERS_TABLE() {
    return '
<table class="sortable table table-striped alignleft" style="width:100%;"><thead><tr>
<th onclick="sortTable(0)" class="text-nowrap">User</th>
<th onclick="sortTable(1)">Number</th>
<th onclick="sortTable(1)">Words</th>
<th onclick="sortTable(1)">Pageviews</th>
</tr></thead>
<tbody>
<tr><td><a href="users.php?user=Subas%20Chandra%20Rout">Subas Chandra Rout</a></td><td>254</td><td>51,506</td><td>28,458</td></tr>
<tr><td><a href="users.php?user=Avicenno">Avicenno</a></td><td>20</td><td>3,670</td><td>20,235</td></tr>
<tr><td><a href="users.php?user=Wakkie1379">Wakkie1379</a></td><td>20</td><td>4,710</td><td>38,576</td></tr>
<tr><td><a href="users.php?user=Mr.%20Ibrahem">Mr. Ibrahem</a></td><td>6</td><td>950</td><td>6,824</td></tr>
<tr><td><a href="users.php?user=%D8%B9%D8%B1%D9%8A%D9%86%20%D8%A3%D8%B3%D8%AF%20%D8%A3%D8%A8%D9%88%20%D8%B1%D9%85%D8%A7%D9%86">عرين أسد أبو رمان</a></td><td>3</td><td>573</td><td>3,367</td></tr>
<tr><td><a href="users.php?user=DaSupremo">DaSupremo</a></td><td>3</td><td>1,001</td><td>62</td></tr>
<tr><td><a href="users.php?user=Yannmaco">Yannmaco</a></td><td>1</td><td>184</td><td>2,453</td></tr>
<tr><td><a href="users.php?user=DanielbdZ">DanielbdZ</a></td><td>1</td><td>185</td><td>2</td></tr>
<tr><td><a href="users.php?user=Lilachit82">Lilachit82</a></td><td>1</td><td>194</td><td>0</td></tr>
<tr><td><a href="users.php?user=Keren%20Elad">Keren Elad</a></td><td>1</td><td>160</td><td>0</td></tr>
<tr><td><a href="users.php?user=Nathanegd">Nathanegd</a></td><td>1</td><td>276</td><td>0</td></tr>
<tr><td><a href="users.php?user=Adir%20Ohayon">Adir Ohayon</a></td><td>1</td><td>5,738</td><td>122</td></tr>
</tbody><tfoot></tfoot></table>';
};
//==========================
?>