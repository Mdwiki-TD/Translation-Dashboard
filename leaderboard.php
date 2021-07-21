<?PHP
//-----------------
require ('header1.php');
require ('tables.php');
require ('functions.php');
require ('langcode.php');
//-----------------
//=============================
$sql_t = 'select user,lang,word,target 
from pages
#where target != "";
';
#-----------------
$sql_u = quary2($sql_t);
//-----------------
$user_process_tab = array();
//-----------------
$Articles_number = 0;
$sql_users_tab = array();
$sql_users_tab_word = array();
//-----------------
$sql_Languages_tab = array();
//-----------------
foreach ( $sql_u AS $id => $table ) {
    #------------
    $target = $table['target'];
    $user = $table['user'];
    $word = number_format( $table['word']);
    $lang = $table['lang'];
    #------------
    if ($target != '') {
        // completd translations
        $lang_in = isset($sql_Languages_tab[$lang]) ? $sql_Languages_tab[$lang] : "";
        if ($lang_in == '') { $sql_Languages_tab[$lang] = 0; };
        #------------
        $sql_Languages_tab[$lang] = $sql_Languages_tab[$lang] + 1 ;
        #------------
        $user_in = isset($sql_users_tab[$user]) ? $sql_users_tab[$user] : "";
        if ($user_in == '') {
            $sql_users_tab[$user] = 0;
            $sql_users_tab_word[$user] = 0;
            };
        //---------------------
        $Articles_number = $Articles_number + 1 ;
        $sql_users_tab[$user] = $sql_users_tab[$user] + 1 ;
        $sql_users_tab_word[$user] = $sql_users_tab_word[$user] + $word ;
        //---------------------
    } else {
        // Translations in process
        $user2_in = isset($user_process_tab[$user]) ? $user_process_tab[$user] : "";
        if ($user2_in == '') {
            $user_process_tab[$user] = 0;
        };
        //---------------------
        $user_process_tab[$user] = $user_process_tab[$user] + 1;
        //---------------------
        
    }; 
};
#-----------------
#-----------------
//=============================
print '
<section class="leaderboard container">
<h1 class="text-center">Leaderboard</h1>
<div class="text-center clearfix">
<span class="col-sm-4" style="width:23%;">';
//=============================
$sato = '
<h3>Numbers</h3>
<table class="sortable table table-striped alignleft">
<tr>
<th onclick="sortTable(0)" class="text-nowrap">Type</th>
<th onclick="sortTable(1)">Number</th>
</tr>
';
#-----------------
$sato .= '<tr><td><b>Users</b></td><td>' .     count($sql_users_tab)     . '</td></tr>';
$sato .= '<tr><td><b>Articles</b></td><td>' .  $Articles_number         . '</td></tr>';
$sato .= '<tr><td><b>Languages</b></td><td>' . count($sql_Languages_tab) . '</td></tr>';
$sato .= '<tr><td><b>Pageviews</b></td><td>' . number_format($views_table->{'total'}) . '</td></tr>';
$sato .= '</table>';
#-----------------
print $sato;
#-----------------
print '</span>
<span class="leaderboard-column col-sm-4">
';
#-----------------
//=============================
function Make_users_table() {
    //---------------
    global $views_table;
    global $sql_users_tab;
    global $sql_users_tab_word;
    #-----------------
    $text = '
    <h3>Top users by number of translations</h3>
    <table class="sortable table table-striped alignleft">
    <tr>
    <th onclick="sortTable(0)" class="text-nowrap">User</th>
    <th onclick="sortTable(1)">Number</th>
    <th onclick="sortTable(1)">Words</th>
    <th onclick="sortTable(1)">Pageviews</th>
    </tr>
    ';
    #-----------------
    arsort($sql_users_tab);
    #-----------------
    foreach ( $sql_users_tab as $user => $number ) {
        //if ($user != 'test') { 
            //------------------------------
            $view = $views_table->{'byuser'};
            $views = $view->{$user};
            $words = $sql_users_tab_word[$user];
            //------------------------------
            $use = rawurlEncode($user);
            $use = str_replace ( '+' , '_' , $use );
            //------------------------------
            $text .= '
        <tr>
            <td><a target="" href="users.php?user=' . $use . '">' . $user . '</a></td>
            <td>' . $number . '</td>
            <td>' . number_format($words) . '</td>
            <td>' . number_format($views) . '</td>
        </tr>
            ';
        //};
    };
    #-----------------
    $text .= '
</table>';
    #-----------------
    return $text;
}
//=============================
print Make_users_table();
//=============================
//--------------------------
//--------------------------
//--------------------------
#-----------------
print '</span>
<span class="leaderboard-column col-sm-4">
';
#-----------------
//--------------------------
//=============================
//--------------------------
function Make_lang_table() {
    #------------------------------------------
    global $views_table;
    global $code_to_lang;
    #------------------------------------------
    global $sql_Languages_tab;
    arsort($sql_Languages_tab);
    #------------------------------------------
    $text = '
    <h3>Top languages by number of Articles</h3>
    <table class="sortable table table-striped alignleft">
    <tr>
    <th onclick="sortTable(0)" class="text-nowrap">Language</th>
    <th onclick="sortTable(2)">Count</th>
    <th onclick="sortTable(2)">Pageviews</th>
    </tr>
    ';
    #-----------------
    foreach ( $sql_Languages_tab as $langcode => $comp ) {
        //-----------------------
        # Get the Articles numbers
        //-----------------------
        if ( $comp > 0 ) {
            //-----------------------
            $langname  = isset($code_to_lang[$langcode]) ? $code_to_lang[$langcode] : $langcode;
            //-----------------------
            $view = $views_table->{'bylang'}->{$langcode}->{'total'};
            //------------------------------
            //-----------------------
            if ($comp != 0) {
                $text .= '
            <tr>
                <td><a target="" href="langs.php?langcode=' . $langcode . '">' . $langname . '</a></td>
                <td>' . $comp . '</td>
                <td>' . number_format($view) . '</td>
                ';
            //-----------------------
            if ( $_SERVER['SERVER_NAME'] != 'mdwiki.toolforge.org' ) { 
                $text .= '<td><a target="_blank" href="https://' . $langcode . '.wikipedia.org/wiki/Category:Translated_from_MDWiki">(cat)</a></td>';
            };
            //=============================
            //-----------------------
                $text .= '
            </tr>';
            };
            //-----------------------
        };
    };
    #-----------------
    $text .= '
</table>';
    #-----------------
    return $text;
}
//=============================
print Make_lang_table();
//=============================
#-----------------
print '
</span>
</div>

';
#-----------------
//-----------------
//-----------------
?>
<?PHP
//-----------------
//-----------------
print '<br/>';
print '<h1 class="text-center">Translations in process</h1>';
//-----------------
//--------------------------
//=============================
function Make_Pinding_table() {
    #-----------------
    global $user_process_tab;
    #-----------------
    $text = '
<span class="col-sm-4" style="width:38%;">
    <h3>Number of translations</h3>
    <table class="sortable table table-striped alignleft">
    <tr>
    <th onclick="sortTable(0)" class="text-nowrap">User</th>
    <th onclick="sortTable(1)">Number</th>';
    
    #-----------------
    if ($_GET['test'] != '') $text .= '<th onclick="sortTable(1)">comp</th><th onclick="sortTable(1)">all</th>';
    #-----------------
    $text .= '
    </tr>
    ';
    #-----------------
    arsort($user_process_tab);
    #-----------------
    foreach ( $user_process_tab AS $user => $pinde ) {
        if ($user != 'test') { 
            //------------------------------
            $use = rawurlEncode($user);
            $use = str_replace ( '+' , '_' , $use );
            //------------------------------
            if ($pinde > 0 ) {
                //------------------------------
                $text .= '<tr>
                <td><a target="" href="users.php?user=' . $use . '">' . $user . '</a></td>
                <td>' . $pinde . '</td>';
                $text .= '</tr>';
            };
        };
    };
    #-----------------
    $text .= '
</table></span>';
    #-----------------
    return $text;
}
//=============================
//-----------------
print Make_Pinding_table();
//-----------------
print "</div>";
//-----------------

print "</main>
<!-- Footer 
<footer class='app-footer'>
</footer>
-->

</body>
</html>
</div>"
//-----------------

?>