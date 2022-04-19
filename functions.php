<?PHP
//--------------------
// require('tables.php');
// include_once('login5.php');
//-------------------- 
include_once('func_2nd.php');
//--------------------
$projects_dirr1 = '/mnt/nfs/labstore-secondary-tools-project';
//--------------------
if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'localhost') { 
    $projects_dirr1 = '/master';
};
//--------------------
$test = $_REQUEST['test'];
//-------------------- 
/*
$prefilled_requests = [] ;
//-------------------- 
function get_request ( $key , $default = "" )  {
    if ( isset ( $prefilled_requests[$key] ) ) return $prefilled_requests[$key] ;
    if ( isset ( $_REQUEST[$key] ) ) return str_replace ( "\'" , "'" , $_REQUEST[$key] ) ;
    return $default ;
};*/
//==========================
function doApiQuery_localhost( $params ) {
    $endPoint = "https://"."mdwiki.org/w/api.php";
    test_print("<br>doApiQuery_localhost:<br>");
    $url = $endPoint . "?" . http_build_query( $params );

    $ch = curl_init( $url );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    $output = curl_exec( $ch );
    curl_close( $ch );
    //------------------
    test_print("<br>output:<br>$output");
    //------------------
    $result = json_decode( $output, true );
    return $result;
};
//==========================
function get_cat_from_cach( $cat ) {
    $RTTtext = file_get_contents("cash/$cat.json");
    //--------------------
    $RTT = json_decode( $RTTtext );
    //--------------------
    $liste = $RTT->list;
    //--------------------
    test_print("<br>get_cat_from_cach: liste size:" . sizeof($liste) );
    //--------------------
    return $liste;
    //--------------------
};
//==========================
function strstartswithn ( $haystack, $needle ) {
  return strpos( $haystack , $needle ) === 0;
};
//==========================
//--------------------
function quary_local_old($quae) {
    //------------
    try {
        // إجراء الإتصال
        $db = new PDO( 
                "mysql:host=localhost:3306;dbname=mdwiki", 
                'root', 
                'root11'
                );
        //--------------------
        //--------------------
        $q = $db->prepare($quae);
        //--------------------
        $q->execute();
        $result = $q->fetchAll();
        return $result;
        //--------------------
    } 
    catch(PDOException $e) {
        echo $quae . "<br>" . $e->getMessage();
    }

    //--------------------
    // إغلاق الإتصال
    $db = null;
    //--------------------
};
//--------------------
//==========================
function sqlquary_localhost($quae) {
    //--------------------
    $host = 'localhost:3306';
    $dbname = "mdwiki";
    //--------------------
    try {
        // إجراء الإتصال
        $db = new PDO(
                "mysql:host=$host;dbname=$dbname", 
                'root', 
                'root11'
                );
        //--------------------
        $q = $db->prepare($quae);
        $q->execute();
        $result = $q->fetchAll();
        //--------------------
        return $result;
    } 
    catch(PDOException $e) {
        echo $quae . "<br>" . $e->getMessage();
    }
    //--------------------
    // إغلاق الإتصال
    $db = null;
    //--------------------
};
//--------------------
function quary($quae) {
    //--------------------
    // if ( isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'localhost' ) { 
        // $sql_u = sqlquary_localhost($quae);
        // return $sql_u;
    // };
    //--------------------
    $ts_pw = posix_getpwuid(posix_getuid()); 
    // replica.my.cnf
    $ts_mycnf = parse_ini_file($ts_pw['dir'] . "/replica.my.cnf");
    //--------------------
    $host = 'tools.db.svc.wikimedia.cloud';
    $dbname = $ts_mycnf['user'] . "__mdwiki";
    //--------------------
    try {
        // إجراء الإتصال
        $db = new PDO(
                "mysql:host=$host;dbname=$dbname", 
                $ts_mycnf['user'], 
                $ts_mycnf['password']
                );
        //--------------------
        unset($ts_mycnf, $ts_pw);
        //--------------------
        $q = $db->prepare($quae);
        //--------------------
        $q->execute();
        $result = $q->fetchAll();
        return $result;
        //--------------------
    } 
    catch(PDOException $e) {
        echo $quae . "<br>" . $e->getMessage();
    }

    //--------------------
    // إغلاق الإتصال
    $db = null;
    //--------------------
};
//--------------------
function quary2($quae) {
    //--------------------
    if ( isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'localhost' ) { 
        $sql_u = sqlquary_localhost($quae);
    } else {
        $sql_u = quary($quae);
    };
    //--------------------
    $sql_result = array();
    //--------------------
    $n = 0;
    //--------------------
    foreach ( $sql_u AS $id => $row ) {
        $ff = array();
        $n = $n + 1 ;
        foreach ( $row AS $nas => $value ) {
            $ff[$nas] = $value;
        };
        $sql_result[$n] = $ff;
    };
    return $sql_result;
}; 
//--------------------
$my_years = array();
//--------------------
function years_start() {
    //--------------------
    global $my_years;
    //--------------------
    $years_q = "select
    CONCAT(left(pupdate,4)) as year
    from pages where pupdate != ''
    group by left(pupdate,4)
    ;";
    $years = quary2($years_q);
    //----
    /*if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'localhost') { 
        $years = array ( 0 => array ( 'year' => '2021', 0 => '2021', ), 1 => array ( 'year' => '2022', 0 => '2022', ), );
    };*/
    //----
    $f = 0;
    //----
	$tt = '';
    //----
    $lines_1 = '';
    $lines = '<ul class="nav nav-tabs">
    <li></li>
    <li></li>
    <li></li>
    ';
    //----
    $y_req = $_REQUEST['year'];
    //----
    $active_done = false;
    //----
    foreach ( $years AS $Key => $table ) {
        //--------------------------
        $year = $table['year'];
        $my_years[] = $year;
        //--------------------------
        if ( $f != 0) { $tt = ' - ';};
		$f = $f + 1;
        //--------------------------
        $url = "<a href='leaderboard.php?year=$year'>$year</a>";
        $lines_1 .= "$tt<div class='menu_item'>$url</div>";
        //--------------------------
        $active = '';
        if ( $active_done == false ) {
           if ( $y_req == $year or $y_req == '' ) {
            $active = 'active';
            $active_done = true;
         };
        };
        //--------------------------
        $lines .= "<li class='menu_item $active'><a href='leaderboard.php?year=$year'>$year</a></li>";
        //--------------------------
        $lines .= "
    ";
    };
    //----
    $lines .= '</ul>';
    $texte = "
    <div class='tab-content'>
        $lines
    </div>";
    //----
    return $texte;
    //----
};
//--------------------
function months_start() {
    //--------------------
    global $my_years;
    //--------------------
    $y_line = years_start();
    //--------------------
    $calendar = 'calendarm.php';
    //--------------------
    $months_qu = "select
    CONCAT(left(pupdate,7)) as month, CONCAT(left(pupdate,4)) as year
    from pages where pupdate != ''
    group by left(pupdate,7)
    ;";
    $months = quary2($months_qu);
    //----
    $months_line = '';
    //----
	$months_urls = array();
    //----
    foreach ( $months AS $Key => $table ) {
        $year = $table['year'];
        $month = $table['month'];
        $last_month = $table['month'];
		//-----------------------------
        $month_url = "<a href='$calendar?month=$month&year=$year'>$month</a>";
        $months_urls[$month] = $month_url;
		//-----------------------------
    };
    //----
    // $months_line = implode(' - ', $months_urls);
    //----
    $lines_by_year = array();
    $lines_by_year_ul = array();
    //----
    $number_months = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
    //----
    foreach ( $my_years AS $kk => $year ) {
        //-----------------------------
        if ( !isset($lines_by_year_ul[$year]) ) { $lines_by_year_ul[$year] = ''; };
        if ( !isset($lines_by_year[$year]) ) { $lines_by_year[$year] = ''; };
        //-----------------------------
        foreach ( $number_months AS $Key => $month ) {
            //-----------------------------
            $month_y = "$year-$month";
            $line = $month_y;
            //-----------------------------
            if ( isset($months_urls[$month_y]) ) {
                $line = $months_urls[$month_y];
            };
            //-----------------------------
            $lines_by_year[$year] .= "<div class='col-sm-1 menu_item2 colsm5'>$line</div>";
            //-----------------------------
            $lines_by_year_ul[$year] .= "<li>$line</li>";
            //-----------------------------
        };
    };
    //----
    $y_req = $_REQUEST['year'];
    //----
    $months_lines = '';
    //----
    foreach ($lines_by_year AS $ii => $y ) {
        //-----------------------------
        if ($y_req == $ii or $y_req == '') {
            $months_lines .= $y;
            break;
        };
        
    }
    $texte = "
    $y_line
    <div class='panel panel-default' style='font-weight: bold;'>
            <div class='panel-body'>
                $months_lines
            </div>
    </div>
    ";
    //--------------------
    //--------------------
    $months_lines_ul = implode('<br>', $lines_by_year_ul);
    //--------------------
    $texte_ul = "
    $y_line
    <ul class='nav nav-pills'>
        $months_lines_ul
    </ul>
    ";
    //----
    return $texte;
    //----
};
//--------------------
//-------------------- 

?>