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
// include_once 'auth/api.php';
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

function open_json_file($file_path) {
    $new_list = array();
    // Check if the file exists
    if (!is_file($file_path)) {
        // Handle the case when the file does not exist
        test_print("$file_path does not exist<br>");
        return $new_list; // Return an empty list
    }

    // Attempt to read the file contents
    $text = file_get_contents($file_path);

    // Check if file_get_contents was successful
    if ($text === false) {
        // Handle the case when file_get_contents fails
        test_print("Failed to read file contents from $file_path<br>");
        return $new_list; // Return an empty list
    }

    // Attempt to decode JSON
    $data = json_decode($text, true);

    // Check if json_decode was successful
    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        // Handle the case when json_decode fails
        test_print("Failed to decode JSON from $file_path<br>");
        return $new_list; // Return an empty list
    }
    return $data;
}

function get_cat_from_cache($cat) {
    // Initialize an empty array for the list
    $empty_list = array();

    // Construct the file path
    $file_path = "cats_cash/$cat.json";

    $new_list = open_json_file($file_path);

    // Check if 'list' key exists in the decoded JSON
    if (!isset($new_list['list']) || !is_array($new_list['list'])) {
        // Handle the case when 'list' key is missing or not an array
        test_print("Invalid format in JSON file $file_path<br>");
        return $empty_list; // Return an empty list
    }
    $data = array();
    // Process the list
    foreach ($new_list['list'] as $key => $value) {
        // Check conditions
        if (!preg_match('/^(Category|File|Template|User):/', $value) && !preg_match('/\(disambiguation\)$/', $value)) {
            $data[] = $value;
        }
    }

    // Print list length
    test_print("<br>get_cat_from_cache: list length: " . count($data));

    return $data;
}

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
		$resa = get_url_with_params( $params );
        //---
		/*
		if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'localhost') {
			//---
			$resa = get_url_with_params( $params );
			//---
		} else {
			$resa = get_api_php($params);
		};*/
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
function get_mdwiki_cat_members( $cat, $use_cache=false, $depth=0 ) {
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
            if ($use_cache || $_SERVER['SERVER_NAME'] == 'localhost' ) {
				$all = get_cat_from_cache( $cat1 );
                if (empty($all)) $all = get_categorymembers($cat1);
			} else {
				$all = get_categorymembers( $cat1 );
                if (empty($all)) $all = get_cat_from_cache($cat1);
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
    // remove duplicates from $titles
    $titles = array_unique( $titles );
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

function get_cat_exists_and_missing($cat, $camp, $depth, $code, $use_cache = false)
{
    $members_to = get_mdwiki_cat_members($cat, $use_cache = $use_cache, $depth = $depth);
    test_print("<br>members_to size:" . count($members_to));
    $members = array();
    foreach ($members_to as $mr) {
        $members[] = $mr;
    };
    test_print("<br>members size:" . count($members));
    $json_file = "cash_exists/$code.json";

    $exists = open_json_file($json_file);

    test_print("<br>$json_file: exists size:" . count($exists));

    // Find missing elements
    // $missing = array_diff($members, $exists);
    $missing = array();
    foreach ($members as $mem) {
        if (!in_array($mem, $exists)) $missing[] = $mem;
    };

    // Remove duplicates from $missing
    $missing = array_unique($missing);

    // Calculate length of exists
    $exs_len = count($members) - count($missing);

    $results = array(
        "len_of_exists" => $exs_len,
        "missing" => $missing
    );
    test_print("<br>end of get_cat_exists_and_missing <br>===============================<br>");
    return $results;
}

