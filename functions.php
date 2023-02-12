<?PHP
//---
// require('tables.php');
// include_once('login5.php');
//--- 
include_once('func_2nd.php');
//---
function add_quotes($str) {
	// if str have ' then use "
	// else use '
	$value = "'$str'";
	if (preg_match("/[']+/", $str)) $value = '"$str"';
	return $value;
};
//---
// $test = $_REQUEST['test'];
//---
function get_request( $key, $value ) {
    $uu = isset($_REQUEST[$key]) ? $_REQUEST[$key] : $value;
    return $uu;
};
//---
function make_input_group( $label, $id, $value, $required) {
    $val2 = add_quotes($value);
    $str = "
    <div class='col-md-3'>
        <div class='input-group mb-3'>
            <div class='input-group-prepend'>
                <span class='input-group-text'>$label</span>
            </div>
            <input class='form-control' type='text' name='$id' value=$val2 $required/>
        </div>
    </div>";
    return $str;
};
//---
function make_drop_d($tab, $cat, $id, $add) {
    //---
    $lines = "";
    //---
    foreach ( $tab AS $dd ) {
        //---
        $se = '';
        //---
        if ( $cat == $dd ) $se = 'selected';
        //---
        $lines .= "
	    <option value='$dd' $se>$dd</option>
		";
        //---
    };
    //---
	$sel_line = "";
	//---
    if ($add != '' ) {
	    $sel = "";
	    if ( $cat == $add ) $sel = "celected";
        $sel_line = "<option value='$add' $sel>$add</option>";
    }
	//---
    $texte = "
        <select dir='ltr' id='$id' name='$id' class='form-select'>
            $sel_line
			$lines
        </select>";
    //---
    return $texte;
    //---
};
//---
function strstartswithn ($text, $word) {
    return strpos($text, $word) === 0;
};
//---
function strendswith($text, $end) {
    return substr($text, -strlen($end)) === $end;
};
//---
function get_url_with_params( $params ) {
	//---
    $endPoint = "https://"."mdwiki.org/w/api.php";
    $url = $endPoint . "?" . http_build_query( $params );
	//---
    test_print("<br>get_url_with_params:$url<br>");
	//---
	$output = file_get_contents($url);
	//---
    // $ch = curl_init( $url );
    // curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    // $output = curl_exec( $ch );
    // curl_close( $ch );
    //---
    // test_print("<br>output:<br>$output");
    //---
    $result = json_decode( $output, true );
    //---
    return $result;
};
//---
function quary_local_old($quae) {
    //---
    try {
        // إجراء الإتصال
        $db = new PDO( 
                "mysql:host=localhost:3306;dbname=mdwiki", 
                'root', 
                'root11'
                );
        //---
        //---
        $q = $db->prepare($quae);
        //---
        $q->execute();
        $result = $q->fetchAll();
        return $result;
        //---
    } 
    catch(PDOException $e) {
        echo $quae . "<br>" . $e->getMessage();
    }

    //---
    // إغلاق الإتصال
    $db = null;
    //---
};
//---
function sqlquary_localhost($quae) {
    //---
    $host = 'localhost:3306';
    $dbname = "mdwiki";
    //---
    try {
        // إجراء الإتصال
        $db = new PDO(
                "mysql:host=$host;dbname=$dbname", 
                'root', 
                'root11'
                );
        //---
        $q = $db->prepare($quae);
        $q->execute();
        //---
        $result = $q->fetchAll();
        //---
        return $result;
    } 
    catch(PDOException $e) {
        echo $quae . "<br>" . $e->getMessage();
    }
    //---
    // إغلاق الإتصال
    $db = null;
    //---
};
//---
function quary($quae) {
    //---
    // if ( isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'localhost' ) { 
        // $sql_u = sqlquary_localhost($quae);
        // return $sql_u;
    // };
    //---
    $ts_pw = posix_getpwuid(posix_getuid()); 
    // replica.my.cnf
    $ts_mycnf = parse_ini_file($ts_pw['dir'] . "/replica.my.cnf");
    //---
    $host = 'tools.db.svc.wikimedia.cloud';
    $dbname = $ts_mycnf['user'] . "__mdwiki";
    //---
    try {
        // إجراء الإتصال
        $db = new PDO(
                "mysql:host=$host;dbname=$dbname", 
                $ts_mycnf['user'], 
                $ts_mycnf['password']
                );
        //---
        unset($ts_mycnf, $ts_pw);
        //---
        $q = $db->prepare($quae);
        //---
        $q->execute();
        $result = $q->fetchAll();
        return $result;
        //---
    } 
    catch(PDOException $e) {
        echo $quae . "<br>" . $e->getMessage();
		return false;
    };
    //---
    // إغلاق الإتصال
    $db = null;
    //---
};
//---
function quary_a($qua) {
    if ( isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'localhost' ) { 
        $us = sqlquary_localhost($qua);
    } else {
        $us = quary($qua);
    };
    return $us;
};
//---
function quary2($quae) {
    //---
    if ( isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'localhost' ) { 
        $sql_u = sqlquary_localhost($quae);
    } else {
        $sql_u = quary($quae);
    };
    //---
    $sql_result = array();
    //---
    $n = 0;
    //---
    foreach ( $sql_u AS $id => $row ) {
        $ff = array();
        $n = $n + 1 ;
        foreach ( $row AS $nas => $value ) {
            $ff[$nas] = $value;
        };
        $sql_result[$n] = $ff;
    };
    //---
    unset($sql_u);
    //---
    return $sql_result;
}; 
//---
$my_years = null;
//---
function get_my_years() {
    //---
    // global $my_years;
    //---
	$my_years1 = array();
	//---
	$years_q = "select
	CONCAT(left(pupdate,4)) as year
	from pages where pupdate != ''
	group by left(pupdate,4)
	;";
	$years = quary2($years_q);
	//---
	foreach ( $years AS $Key => $table ) {
		$year = $table['year'];
		$my_years1[] = $year;
	};
	//---
    return $my_years1;
};
//---
function years_start($page) {
    //---
    global $my_years;
    //---
	if ($my_years == null) $my_years = get_my_years();
	$tt = '';
    //---
    $y_req = $_REQUEST['year'];
    //---
    $lines = "";
    //---
    foreach ( $my_years AS $Key => $year ) {
        //---
        $active = '';
        //---
        if ( $y_req == $year ) $active = 'active';
        //---
        $lines .= "
	<li class='nav-item menu_item'><a class='nav-link $active' href='leaderboard.php?year=$year'>$year</a></li>
		";
        //---
    };
    //---
	$activeold = "";
	//---
	if ( $y_req == '' ) $activeold = "active";
	//---
    $texte = "
	<div class='tab-content'>
		<ul class='nav nav-tabs nav-justified'>
			<li class='nav-item menu_item'><a class='nav-link $activeold' href='leaderboard.php'>All</a></li>
			$lines
			</ul>
	</div>";
    //---
    return $texte;
    //---
};
//---
function months_start() {
    //---
    global $my_years;
    //---
	if ($my_years == null) $my_years = get_my_years();
    //---
    $calendar = 'calendar.php';
    //---
    $months_qu = "select
    CONCAT(left(pupdate,7)) as month, CONCAT(left(pupdate,4)) as year
    from pages where pupdate != ''
    group by left(pupdate,7)
    ;";
    $months = quary2($months_qu);
    //---
    $months_line = '';
    //---
	$months_urls = array();
    //---
    foreach ( $months AS $Key => $table ) {
        $year = $table['year'];
        $month = $table['month'];
        // $last_month = $table['month'];
		//---
        $month_url = "<a cass='spannowrap' href='$calendar?month=$month&year=$year'>$month</a>";
        $months_urls[$month] = $month_url;
		//---
    };
    //---
    // $months_line = implode(' - ', $months_urls);
    //---
    $lines_by_year = array();
    $lines_by_year_ul = array();
    //---
    $number_months = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
    //---
    foreach ( $my_years AS $kk => $year ) {
        //---
        if ( !isset($lines_by_year_ul[$year]) ) $lines_by_year_ul[$year] = '';
        if ( !isset($lines_by_year[$year]) )    $lines_by_year[$year] = '';
        //---
        foreach ( $number_months AS $Key => $month ) {
            //---
            $month_y = "$year-$month";
            //---
            $line = isset($months_urls[$month_y]) ? $months_urls[$month_y] : "<span cass='spannowrap'>$year-$month</span>";
            //---
            $lines_by_year[$year] .= "<div class='col-md-1 colsm5'>$line</div>
                ";
            //---
            $lines_by_year_ul[$year] .= "<li class='nav-item menu_item'>$line</li>";
            //---
        };
    };
    //---
    $y_req = isset($_REQUEST['year']) ? $_REQUEST['year'] : date("Y");
    //---
    $months_lines = isset($lines_by_year[$y_req]) ? $lines_by_year[$y_req] : '';
    //---
    $texte = "
    <div class='card' style='font-weight: bold;'>
		<div class='card-body'>
				<div class='row'>
					$months_lines
				</div>
		</div>
    </div>
    ";
    //---
    $mo = isset($lines_by_year_ul[$y_req]) ? $lines_by_year_ul[$y_req] : '';
    //---
    $texte_ul = "
	<div class='card' style='font-weight: bold;'>
		<ul class='nav nav-tabs nav-justified'>
			$mo
		</ul>
	</div>
    ";
    //---
    return $texte_ul;
    //---
};
//---
$usrs = array();
//---
$usrs1 = quary_a('select user from coordinator;');
//---
foreach ( $usrs1 AS $id => $row )	$usrs[] = $row['user'];
//---
// echo var_dump($usrs);
//---
// $usrs = Array("Mr. Ibrahem", "Doc James");
//---

//--- 

?>