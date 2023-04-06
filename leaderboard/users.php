<?PHP
//---
require('lead_help.php');
//---
$test = isset($_REQUEST['test']) ? $_REQUEST['test'] : '';
$mainuser = isset($_REQUEST['user']) ? $_REQUEST['user'] : '';
$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : null;
//---
if ($mainuser == $username) {
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
function make_user_table($user_main, $test, $limit) {
    //---
    global $table_of_views;
    global $dd, $dd_Pending;
    //---
    $user_main = rawurldecode( str_replace ( '_' , ' ' , $user_main ) );
    //---
    $count_sql = "select count(title) as count from pages where user = '$user_main';";
    //---
    $count_query = execute_query_2($count_sql);
    //---
    $user_count = $count_query[1]['count'];
    //---
    unset($count_query);
    //---
    if ($test != '' ) echo "<br>user_count : $user_count<br>";
    //---
    $done = 0;
    $offset = 0;
    //---
    while ($done < $user_count) {
        // echo "offset: $offset.";
        // views (target, countall, count2021, count2022, count2023, lang)
        $quaa_view = "select p.target,v.countall
        from pages p,views v
        where p.user = '$user_main'
        and p.target = v.target
        limit 200
        offset $offset
        ;
        ";
        //---
        $views_query = execute_query_2($quaa_view);
        //---
        if (count($views_query) == 0) { $done = $user_count;};
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
    $quaa = "select * from pages where user = '$user_main'";
    //---
    if ($limit != '' && is_numeric($limit)) $quaa = $quaa . " limit $limit";
    //---
    if ($test != '') echo $quaa;
    //---
    $sql_result = execute_query_2($quaa);
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
make_user_table($mainuser, $test, $limit);
//---
krsort($dd);
//---
$tat = make_table_lead($dd, $tab_type='translations', $views_table = $table_of_views, $page_type='users', $user=$mainuser, $lang='');
//---
$table1 = $tat['table1'];
$table2 = $tat['table2'];
//---
$man = make_mdwiki_user_url($mainuser);
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
$table_pnd = make_table_lead($dd_Pending, $tab_type='pending', $views_table = $table_of_views, $page_type='users', $user=$mainuser, $lang='');
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