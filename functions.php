<?PHP
//---
include_once('actions/html.php');
include_once('actions/wiki_api.php');
include_once('actions/mdwiki_api.php');
include_once('actions/mdwiki_sql.php');
//---
function strstartswithn($text, $word) {
    return strpos($text, $word) === 0;
};
//---
function strendswith($text, $end) {
    return substr($text, -strlen($end)) === $end;
};
//---
function get_request_1( $key, $i ) {
    $uu = isset($_REQUEST[$key][$i]) ? $_REQUEST[$key][$i] : '';
    return $uu;
};
//---
function get_request( $key, $value ) {
    $uu = isset($_REQUEST[$key]) ? $_REQUEST[$key] : '';
    $uu = ($uu != '') ? $uu : $value;
    return $uu;
};
//---
function test_print($s) {
    global $test;
    if ($test != '') print $s;
};
//---
function get_my_years() {
    //---=
	$my_years1 = array();
	//---
	$years_q = "select
	CONCAT(left(pupdate,4)) as year
	from pages where pupdate != ''
	group by left(pupdate,4)
	;";
	$years = execute_query($years_q);
	//---
	foreach ( $years AS $Key => $table ) {
		$year = $table['year'];
		$my_years1[] = $year;
	};
	//---
    return $my_years1;
};
//---
$usrs = array();
//---
$usrs1 = execute_query('select user from coordinator;');
//---
foreach ( $usrs1 AS $id => $row )	$usrs[] = $row['user'];
//---
?>