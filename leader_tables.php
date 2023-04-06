<?PHP
//---
include_once('tables.php');
include_once('functions.php');
include_once('langcode.php');
//---
include_once('sql_tables.php'); // $sql_qids $cat_titles $cat_to_camp $camp_to_cat
//---
$year      = isset($_REQUEST['year']) ? $_REQUEST['year']   : 'all';
$camp      = isset($_REQUEST['camp']) ? $_REQUEST['camp'] : 'all';
$project   = isset($_REQUEST['project']) ? $_REQUEST['project'] : 'all';
//---
if ($camp == 'all' && isset($_REQUEST['cat'])) $camp = isset($cat_to_camp[$_REQUEST['cat']]) ? $cat_to_camp[$_REQUEST['cat']] : 'all';
//---
$camp_cat  = isset($camp_to_cat[$camp]) ? $camp_to_cat[$camp] : '';
//---
$qua_all_part1_group = "select
p.target, p.cat, p.lang, p.word, year(p.pupdate) as pup_y, p.user, u.user_group
from pages p, users u
";
//---
$qua_all_part1 = "select
p.target, p.cat, p.lang, p.word, year(p.pupdate) as pup_y, p.user, 
(select u.user_group from users u WHERE p.user = u.username) as user_group
from pages p
";
//---
$qua_all_part2 = "
where p.target != ''
";
//---
if ($camp != 'all' && $camp_cat != '') $qua_all_part2 .= "and p.cat = '$camp_cat' \n";
if ($year != 'all')         $qua_all_part2 .= "and year(p.pupdate) = '$year' \n";
if ($project != 'all') {
    $qua_all_part1 = $qua_all_part1_group;
    $qua_all_part2 .= "and p.user = u.username \n";
    $qua_all_part2 .= "and u.user_group = '$project' \n";
}; 
//---
$qua_all = $qua_all_part1 . $qua_all_part2;
//---
if (isset($_REQUEST['test'])) echo $qua_all;
//---
$Words_total = 0;
$Articles_numbers = 0;
$global_views = 0;
//---
$sql_users_tab = array();
//---
$Users_word_table = array();
//---
$sql_Languages_tab = array();
//---
$all_views_by_lang = array();
//---
$Views_by_users = array();
$Views_by_target = array();
//---
$qua_vi = "select target, countall, count2021, count2022, count2023
from views
;
";
//---
foreach ( execute_query($qua_vi) AS $k => $tab ) {
    $Views_by_target[$tab['target']] = array(
        'all'  => $tab['countall'],
        '2021' => $tab['count2021'],
        '2022' => $tab['count2022'],
        '2023' => $tab['count2023']
    );
};
//---
foreach ( execute_query($qua_all) AS $Key => $teb ) {
    //---
    $cat    = $teb['cat'];
    $lang   = $teb['lang'];
    $user   = $teb['user'];
    $tat    = $teb['target'];
    $word   = $teb['word'];
    //---
    $coco = isset($Views_by_target[$tat][$year]) ? $Views_by_target[$tat][$year] : 0;
    //---
    $Words_total += $word;
    $Articles_numbers += 1;
    $global_views += $coco;
    //---
    if (!isset($all_views_by_lang[$lang])) $all_views_by_lang[$lang] = 0;
    $all_views_by_lang[$lang] += $coco;
    //---
    if (!isset($sql_Languages_tab[$lang]))       $sql_Languages_tab[$lang] = 0;
    $sql_Languages_tab[$lang] = $sql_Languages_tab[$lang] + 1 ;
    //---
    if (!isset($Users_word_table[$user])) $Users_word_table[$user] = 0;
    $Users_word_table[$user] += $word ;
    //---
    if (!isset($Views_by_users[$user])) $Views_by_users[$user] = 0;
    $Views_by_users[$user] += $coco;
    //---
    if (!isset($sql_users_tab[$user])) $sql_users_tab[$user] = 0;
    $sql_users_tab[$user] = $sql_users_tab[$user] + 1 ;
    //---
    };
//---
function createNumbersTable($c_user, $c_articles, $c_words, $c_langs, $c_views) {
    //---
    $Numbers_table = '
    <table class="sortable table table-striped"> <!-- scrollbody -->
    <thead>
        <tr>
            <th class="spannowrap">Type</th>
            <th>Number</th>
        </tr>
    </thead>
    <tbody>
    ';
    //---
    $Numbers_table .= '<tr><td><b>Users</b></td><td>' .     $c_user        . '</td></tr>
    ';
    $Numbers_table .= '<tr><td><b>Articles</b></td><td>' .  $c_articles . '</td></tr>
    ';
    $Numbers_table .= '<tr><td><b>Words</b></td><td>' .     $c_words     . '</td></tr>
    ';
    $Numbers_table .= '<tr><td><b>Languages</b></td><td>' . $c_langs       . '</td></tr>
    ';
    $Numbers_table .= '<tr><td><b>Pageviews</b></td><td>' . $c_views    . '</td></tr>
    ';
    $Numbers_table .= '
    </tbody>
    </table>';
    //---
    return $Numbers_table;
};
//---
function makeUsersTable() {
    //---
    global $sql_users_tab, $Users_word_table, $Views_by_users;
    //---
    $text = '
    <table class="sortable table table-striped">
        <thead>
            <tr>
                <th class="spannowrap">#</th>
                <th class="spannowrap">User</th>
                <th>Number</th>
                <th>Words</th>
                <th>Pageviews</th>
            </tr>
        </thead>
        <tbody>
    ';
    //---
    arsort($sql_users_tab);
    //---
    $numb = 0;
    //---
    foreach ( $sql_users_tab as $user => $usercount ) {
            //---
            $numb += 1;
            //---
            $views = isset($Views_by_users[$user]) ? number_format($Views_by_users[$user]) : 0;
            $words = isset($Users_word_table[$user]) ? number_format($Users_word_table[$user]) : 0;
            //---
            $use = rawurlEncode($user);
            $use = str_replace ( '+' , '_' , $use );
            //---
            $text .= "
            <tr>
                <td>$numb</td>
                <td><a href='leaderboard.php?user=$use'>$user</a></td>
                <td>$usercount</td>
                <td>$words</td>
                <td>$views</td>
            </tr>
            ";
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
function makeLangTable() {
    //---
    global $lang_code_to_en, $code_to_lang, $sql_Languages_tab, $all_views_by_lang;
    //---
    arsort($sql_Languages_tab);
    //---
    $addcat = $_SERVER['SERVER_NAME'] == 'localhost';
    //---
    $cac = ($addcat == true ) ? '<th>cat</th>' : '';
    //---
    $text = "
    <table class='sortable table table-striped'>
    <thead>
        <tr>
            <th>#</th>
            <th class='spannowrap'>Language</th>
            <th>Count</th>
            <th>Pageviews</th>
            $cac
        </tr>
    </thead>
    <tbody>
    ";
    //---
    $numb=0;
    //---
    foreach ( $sql_Languages_tab as $langcode => $comp ) {
        //---
        # Get the Articles numbers
        //---
        if ( $comp > 0 ) {
            //---
            $numb ++;
            //---
            // $langname  = isset($code_to_lang[$langcode]) ? $code_to_lang[$langcode] : $langcode;
            $langname  = isset($lang_code_to_en[$langcode]) ? "($langcode) " . $lang_code_to_en[$langcode] : $langcode;
            //---
            $view = isset($all_views_by_lang[$langcode]) ? $all_views_by_lang[$langcode] : 0;
            $view = number_format($view);
            //---
            $cac = ($addcat == true ) ? '<td><a target="_blank" href="https://' . $langcode . '.wikipedia.org/wiki/Category:Translated_from_MDWiki">cat</a></td>' : '';
            //---
            if ($comp != 0) {
                $text .= "
            <tr>
                <td>$numb</td>
                <td><a href='leaderboard.php?langcode=$langcode'>$langname</a></td>
                <td>$comp</td>
                <td>$view</td>
                $cac
            </tr>";
            };
            //---
        };
    };
    //---
    $text .= '
    </tbody>
    </table>';
    //---
    return $text;
}
//---

//---
?>