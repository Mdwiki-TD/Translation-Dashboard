<?PHP
//---
if (isset($_REQUEST['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};
//---
require 'tables.php';
require 'langcode.php';
include_once 'functions.php';
include_once 'auth/api.php';
//---
function start_with( $haystack, $needle ) {
    return strpos( $haystack , $needle ) === 0;
};
//---
function get_in_process($missing, $code) {
    $qua = "select * from pages where target = '' and lang = '$code';";
    //---
    $res = execute_query($qua);
    //---
    // echo "<br>";
    // var_export(json_encode($res));
    //--
    $titles = array();
    //---
    foreach ( $res AS $t) {
        if (in_array($t['title'], $missing)) $titles[$t['title']] = $t;
    }
    //---
    // var_export(json_encode($titles));
    //--
    return $titles;
    //---
}
//---
function get_cat_from_cach( $cat ) {
    //---
    $file_path = "cats_cash/$cat.json";
    //---
    if(!is_file($file_path)) {
        test_print("$file_path dont exist<br>");
        return array();
        // file does not exist
    };
    //---
    $RTTtext = file_get_contents($file_path);
    //---
    $RTT = json_decode( $RTTtext, true );
    //---
    $new_liste = array();
    //---
    $liste = $RTT['list'];
    //---
    foreach ($liste as $key => $value) {
        // find if not starts with Category:
        $test_value = preg_match('/^(Category|File|Template|User):/', $value);
        // find if not ends with (disambiguation)
        $test_value2 = preg_match('/\(disambiguation\)$/', $value);
        //---
        if ($test_value == 0 && $test_value2 == 0) {
            $new_liste[] = $value;
        };
    };
    //---
    test_print("<br>get_cat_from_cach: list lenth:" . count($new_liste) );
    //---
    return $new_liste;
    //---
};
//---
function get_categorymembers( $cat ) {
    //---
    $ch = null;
    //---
    if (!start_with($cat , 'Category:')) {
        $cat = "Category:$cat";
    };
    //---
    $params = array(
        "action" => "query",
        "list" => "categorymembers",
        "cmtitle" => "$cat",
        "cmlimit" => "max",
        "cmtype" => "page|subcat",
        "format" => "json"
    );
    //---
	$items = array();
	//---
	$cmcontinue = 'x';
    //---
	while($cmcontinue != '') {
		//---
		if ($cmcontinue != 'x') $params['cmcontinue'] = $cmcontinue;
		//---
        $endPoint = "https://mdwiki.org/w/api.php?" . http_build_query($params);
        //---
        test_print("<br>params:<br>$endPoint"."<br>");
        //---
		if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'localhost') { 
			//---
			$resa = get_url_with_params( $params );
			//---
		} else {
			$resa = doApiQuery($params);
		};
		//---
        if (!isset($resa["query"])) $resa = get_url_with_params( $params );
		//---
		$continue   = $resa["continue"] ?? '';
		$cmcontinue = $continue["cmcontinue"] ?? '';// "continue":{"cmcontinue":"page|434c4f42415a414d|60836",
		//---
		$query = $resa["query"] ?? array();
		$categorymembers = $query["categorymembers"] ?? array();
		$categorymembers = $categorymembers ?? array();
		//---
		// print htmlspecialchars( var_export( $categorymembers, 1 ) );
		//---
		//
		foreach( $categorymembers as $pages ){
			// echo( $pages["title"] . "\n" );
			if ($pages["ns"] == 0 or $pages["ns"] == 14) {
				$items[] = $pages["title"];
			};
		};
	};
    //---
    // $tt = array();
    // $tt['items']    = $items;
    // $tt['continue'] = $cmcontinue;
    //---
    test_print("<br>get_categorymembers() items size:" . count($items) );
    //---
    return $items;
    //---
};
//---
function get_mdwiki_cat_members( $cat, $use_cash=false, $depth=0 ) {
    //---
    $titles = array();
    $cats = array();
	$cats[] = $cat;
    //---
    $depth_done = -1;
    //---
	while (count($cats) > 0 && $depth > $depth_done) {
		$cats2 = array();
		//---
		foreach( $cats as $cat1 ){
            if ($use_cash || $_SERVER['SERVER_NAME'] == 'localhost' ) {
				$all = get_cat_from_cach( $cat1 );
                if (empty($all)) $all = get_categorymembers($cat1);
			} else {
				$all = get_categorymembers( $cat1 );
                if (empty($all)) $all = get_cat_from_cach($cat1);
			};
			//---
			foreach( $all as $title ){
				if (start_with($title , 'Category:')) {
					$cats2[] = $title;
				} else {
					$titles[] = $title;
				};
			//---
		};
		};
		//---
		test_print("<br>cats2 size:" . count($cats2) );
		//---
        $depth_done ++;
		//---
		$cats = $cats2;
		//---
	};
    //---
    test_print("<br>cats size:" . count($cats) );
    //---
    $newtitles = array();
    foreach( $titles as $title ){
        // find if not starts with Category:
        $test_value = preg_match('/^(File|Template|User):/', $title);
        // find if not ends with (disambiguation)
        $test_value2 = preg_match('/\(disambiguation\)$/', $title);
        //---
        if ($test_value == 0 && $test_value2 == 0) {
            $newtitles[] = $title;
        };
    };
    //---
    test_print("<br>newtitles size:" . count($newtitles) );
    test_print("<br>end of get_mdwiki_cat_members <br>===============================<br>");
    //---
    return $newtitles;
    //---
};
//---
function get_cat_members( $cat, $depth, $code, $use_cash=false ) {
    //---
    $members_to = get_mdwiki_cat_members( $cat, $use_cash=$use_cash, $depth=$depth );
    //---
    test_print("<br>members_to size:" . count($members_to) );
    //---
    $members = array();
    //---
    foreach( $members_to as $mr ) {
        //---
        $members[] = $mr;
    }; 
    //---
    test_print("<br>members size:" . count($members) );
    //---
    $exists = array();
    //--- 
    $json_file = "cash_exists/$code.json";
    if(is_file($json_file))  {
        $exists = json_decode(file_get_contents($json_file), true);
    } else {
        // log to console
        // error_log("file $json_file not found", LOG_INFO);
		$exists = array();
    };
    //---
    test_print("<br>$json_file: exists size:" . count($exists) );
    //---
    if ($exists == null) $exists = array();
    //---
    // $missing = array_diff($members,$exists);
    $missing = array();
    //---
    foreach( $members as $mem ) {
        if (!in_array($mem,$exists)) $missing[] = $mem;
    };
    //---
    $exs_len = count($members) - count($missing);
    //---
    $results = array(
        "len_of_exists"=> $exs_len,
        "missing"=> $missing
    );
    //---
    test_print("<br>end of get_cat_members <br>===============================<br>");
    //---
    return $results;
    //---
};
//---
