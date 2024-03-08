<?PHP
//---
require 'lead_help.php';
require 'camps.php';
//---
$test     = $_REQUEST['test'] ?? '';
$mainuser = $_REQUEST['user'] ?? '';
$lang_y = $_REQUEST['lang'] ?? 'All';
$year_y = $_REQUEST['year'] ?? 'All';
//---
if ($mainuser == global_username) {
    echo '<script>
        $(".navbar-nav").find("li.active").removeClass("active");
        $("#myboard").addClass("active");
        </script>
    ';
};
//---
$dd = array();
$dd_Pending = array();
$table_of_views = array();
//---
if (True) {
    $user_main = $mainuser;
    $user_main = rawurldecode( str_replace ('_', ' ', $user_main) );
    //---
    $pages_qua = <<<SQL
        select * from pages where user = '$user_main'
    SQL;
    //---
    $count_sql = <<<SQL
        select count(title) as count from pages where user = '$user_main'
    SQL;
    //---
    if ($lang_y != 'All') {
        $count_sql .= " and lang = '$lang_y'";
        $pages_qua .= " and lang = '$lang_y'";
    };
    //---
    if ($year_y != 'All') {
        $count_sql .= " and YEAR(date) = '$year_y'";
        $pages_qua .= " and YEAR(date) = '$year_y'";
    };
    //---
    $views_qua = <<<SQL
        select p.target, v.countall
        from pages p, views v
        where p.user = '$user_main'
        and p.lang = v.lang
        and p.target = v.target
        limit 200
    SQL;
    //---
    if ($test != '') {
        echo $count_sql . '<br>';
        echo $pages_qua . '<br>';
        echo $views_qua . '<br>';
    }
    //---
};
//---
if ($mainuser != '') {
    //---
    $count_query = execute_query($count_sql);
    //---
    $user_count = $count_query[0]['count'];
    //---
    unset($count_query);
    //---
    if ($test != '' ) echo "<br>user_count : $user_count<br>";
    //---
    $done = 0;
    $offset = 0;
    //---
    while ($done < $user_count) {
        //---
        $quaa_view = $views_qua;
        $quaa_view .= "
        offset $offset
        ";
        //---
        $views_query = execute_query($quaa_view);
        //---
        if (count($views_query) == 0) $done = $user_count;
        //---
        foreach ( $views_query AS $Key => $table ) {
            $countall = $table['countall'];
            $targ = $table['target'];
            $table_of_views[$targ] = $countall;
            //---
            $done += 1;
            //---
        };
        //---
        unset($views_query);
        //---
        $offset += 200;
        //---
    };
    //---
    $sql_result = execute_query($pages_qua);
    //---
    foreach ( $sql_result AS $tait => $tabb ) {
            //---
            $kry = str_replace('-','',$tabb['pupdate']) . ':' . $tabb['lang'] . ':' . $tabb['title'] ;
            //---
            if ( $tabb['target'] != '' ) {
                $dd[$kry] = $tabb;
            } else {
                $dd_Pending[$kry] = $tabb;
            };
            //---
        };
    //---
};
//---
function make_filter_form($user) {
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
    // $result = getUserYearsAndLangs($user);
    // $langs = $result['langs'];
    // $years = $result['years'];
    //---
    $years = array_map('current', execute_query("SELECT DISTINCT LEFT(date, 4) AS year FROM pages WHERE user = '$user'"));
    $langs = array_map('current', execute_query("SELECT DISTINCT lang FROM pages WHERE user = '$user'"));
    //---
    $y2 = makeDropdown($langs, $lang_y, 'lang', 'All');
    $langsDropdown = sprintf($d33, 'Lang', $y2);
    //---
    $y3 = makeDropdown($years, $year_y, 'year', 'All');
    $yearDropdown = sprintf($d33, 'Year', $y3);
    //---
    return <<<HTML
        <form method="get" action="leaderboard.php">
            <input type="hidden" name="user" value="$user" />
            <div class='container g-3'>
                <div class='row content'>
                    <div class="col-md-4">
                        $langsDropdown
                    </div>
                    <div class="col-md-4">
                        $yearDropdown
                    </div>
                    <div class="aligncenter col-md-4">
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
$count_new = count($dd);
//---
$tat = make_table_lead($dd, $tab_type='translations', $views_table = $table_of_views, $page_type='users', $user=$mainuser, $lang='');
//---
$table1 = $tat['table1'];
$table2 = $tat['table2'];
//---
$man = make_mdwiki_user_url($mainuser);
//---
$filter_form = make_filter_form($mainuser);
//---
echo <<<HTML
    <div class='row content'>
        <div class='col-md-3'>$table1</div>
        <div class='col-md-3'><h2 class='text-center'>$man ($count_new)</h2></div>
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
$table_pnd = make_table_lead($dd_Pending, $tab_type='pending', $views_table = $table_of_views, $page_type='users', $user=$mainuser, $lang='');
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