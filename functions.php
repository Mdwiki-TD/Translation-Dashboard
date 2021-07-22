<?PHP
//--------------------
require('tables.php');
//--------------------
function get_request ( $key , $default = "" ) /*:string*/ {
    if ( isset ( $prefilled_requests[$key] ) ) return $prefilled_requests[$key] ;
    if ( isset ( $_REQUEST[$key] ) ) return str_replace ( "\'" , "'" , $_REQUEST[$key] ) ;
    return $default ;
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
    $sql_u = quary($quae);
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
function make_view($target , $lang) {
    //--------------------
    global $views_table;
    //--------------------
    $vav = $views_table->{'bylang'};
    $numb = $vav->{$lang};
    $numb = $numb->{$target};
    //--------------------
    $urln = 'https://'.'pageviews.toolforge.org/?project='. $lang .'.wikipedia.org&platform=all-access&agent=all-agents&redirects=0&range=this-year&pages=' . rawurlEncode($target);
    
    $link = '<a target="_blank" href="' . $urln . '">' . $numb . '</a>';
    //--------------------
    return $link ;
    };
//--------------------
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