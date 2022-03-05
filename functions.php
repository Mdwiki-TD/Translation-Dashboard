<?PHP
//--------------------
// require('tables.php');
// include_once('login5.php');
//-------------------- 
//--------------------
$projects_dirr1 = '/mnt/nfs/labstore-secondary-tools-project';
//--------------------
if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'localhost') { 
    $projects_dirr1 = '/master';
};
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
function years_start() {
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
    $lines = '';
    //----
    $f = 0;
    //----
	$tt = '';
    //----
    foreach ( $years AS $Key => $table ) {
        $year = $table['year'];
        if ( $f != 0) { $tt = ' - ';};
        $lines .= "$tt<div class='menu_item'><a href='leaderboard.php?year=$year'>$year</a></div>";
		$f = $f + 1;
    };
    $texte = "<div class='menu'>$lines</div>";
    
    //----
    /*if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'localhost') { 
        $texte = "<div class='menu'><div class='menu_item'><a href='leaderboard.php?year=2021'>2021</a></div>
<div class='menu_item'> - <a href='leaderboard.php?year=2022'>2022</a></div></div>";
    };*/
    //----
    return $texte;
    //----
};
//--------------------
function months_start() {
    //--------------------
    $months_qu = "select
    CONCAT(left(pupdate,7)) as month
    from pages where pupdate != ''
    group by left(pupdate,7)
    ;";
    $months = quary2($months_qu);
    //----
	/*if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'localhost') { 
		$months = array ( 0 => array ( 'month' => '2021-05', 0 => '2021-05', ), 1 => array ( 'month' => '2021-06', 0 => '2021-06', ), 2 => array ( 'month' => '2021-07', 0 => '2021-07', ), 3 => array ( 'month' => '2021-08', 0 => '2021-08', ), 4 => array ( 'month' => '2021-09', 0 => '2021-09', ), 5 => array ( 'month' => '2021-10', 0 => '2021-10', ), 6 => array ( 'month' => '2021-11', 0 => '2021-11', ), 7 => array ( 'month' => '2021-12', 0 => '2021-12', ), 8 => array ( 'month' => '2022-01', 0 => '2022-01', ), );
	};*/
    //----
    // echo var_export($months);
    //----
    $months_line = '';
    //----
    foreach ( $months AS $Key => $table ) {
        $month = $table['month'];
        $last_month = $table['month'];
        $months_line .= "
<div class='menu_item colsm5'><a href='calendar.php?month=$month'>$month</a></div>";
    };
    $texte = "
<div class='menu'>
<span class='colsm5'>
$months_line
</span>
</div>";
    //----
    return $texte;
    //----
};
//--------------------

//--------------------
function make_view_by_number($target , $numb, $lang) {
    //---------------
    $numb2 = ($numb != '') ? $numb : "?";
    //---------------
    $urln = 'https://' . 'pageviews.toolforge.org/?project='. $lang .'.wikipedia.org&platform=all-access&agent=all-agents&redirects=0&range=this-year&pages=' . rawurlEncode($target);
    //---------------
    $link = '<a target="_blank" href="' . $urln . '">' . $numb2 . '</a>';
    //---------------
    return $link ;
    };
//-----------------
function make_mdwiki_title($tit) {
    $title = $tit;
    if ($title != '') {
        $title2 = rawurlencode( str_replace ( ' ' , '_' , $title ) );
        $title = '<a href="https://mdwiki.org/wiki/' . $title2 . '">' . $title . '</a>';
    };
    return $title;
};
//-------------------- 
function make_cat_url ($ca) {
    $cat = $ca;
    if ($cat != '') {
        $cat2 = rawurlencode( str_replace ( ' ' , '_' , $cat ) );
        $cat = '<a href="https://mdwiki.org/wiki/Category:' . $cat2 . '">Category:' . $cat . '</a>';
    };
    return $cat;
};
//-------------------- 
function make_mdwiki_user_url($ud) {
    $user = $ud;
    if ($user != '') {
        $user2 = rawurlencode( str_replace ( ' ' , '_' , $user ) );
        $user = '<a href="https://mdwiki.org/wiki/User:' . $user2 . '">' . $user . '</a>';
    };
    return $user;
};
//-------------------- 
function make_target_url ($ta , $lang) {
    $target = $ta ;
    if ($target != '') {
        $target2 = rawurlencode( str_replace ( ' ' , '_' , $target ) );
        $target = '<a href="https://' . $lang . '.wikipedia.org/wiki/' . $target2 . '">' . $target . '</a>';
    };
    return $target;
};
//-------------------- 
//-------------------- 

?>