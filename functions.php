<?php
$print_t = false;

if (isset($_REQUEST['test'])) {
    $print_t = true;
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

define('print_te', $print_t);

include_once 'actions/html.php';
include_once 'actions/wiki_api.php';
include_once 'actions/mdwiki_api.php';
include_once 'actions/mdwiki_sql.php';


function load_request() {
    global $lang_to_code, $code_to_lang, $camp_to_cat;
    //---
    $code = $_REQUEST['code'] ?? '';
    //---
    if ($code == 'undefined') $code = "";
    //---
    $code = $lang_to_code[$code] ?? $code;
    $code_lang_name = $code_to_lang[$code] ?? ''; 
    //---
    $cat  = $_REQUEST['cat'] ?? '';
    if ($cat == 'undefined') $cat = "";
    //---
    $camp = $_REQUEST['camp'] ?? '';
    //---
    if ($cat == "" && $camp != "") {
        $cat = $camp_to_cat[$camp] ?? $cat;
    }
    //---
    // if ($cat == "") $cat = "RTT";
    //---
    return [
        'code' => $code,
        'cat' => $cat,
        'code_lang_name' => $code_lang_name
    ];
}
//---
function escape_string($unescaped_string) {
    // Alternative mysql_real_escape_string without mysql connection
    $replacementMap = [
        "\0" => "\\0",
        "\n" => "",
        "\r" => "",
        "\t" => "",
        chr(26) => "\\Z",
        chr(8) => "\\b",
        '"' => '\"',
        "'" => "\'",
        '_' => "\_",
        "%" => "\%",
        '\\' => '\\\\'
    ];

    return \strtr($unescaped_string, $replacementMap);
}
function strstartswithn($text, $word) {
    return strpos($text, $word) === 0;
}

function strendswith($text, $end) {
    return substr($text, -strlen($end)) === $end;
}

function test_print($s) {
    if (print_te) {
        if (gettype($s) == 'string') {
            echo "<br>$s";
        } else {
            echo "<br>";
            print_r($s);
        }
    }
}

function getMyYears() {
    $my_years1 = [];
    
    $years_q = <<<SQL
    SELECT
        CONCAT(left(pupdate, 4)) AS year
    FROM
        pages
    WHERE
        pupdate != ''
    GROUP BY
        left(pupdate, 4)
    SQL;
    
    $years = execute_query($years_q);
    
    foreach ($years as $key => $table) {
        $year = $table['year'];
        $my_years1[] = $year;
    }
    
    return $my_years1;
}

function getUserYearsAndLangs($user) {
    $years_q = <<<SQL
    SELECT
        CONCAT(left(pupdate, 4)) AS year, lang
    FROM
        pages
    WHERE
        pupdate != ''
    AND
        user = '$user'
    GROUP BY
        left(pupdate, 4), lang
    SQL;
    
    $data = execute_query($years_q);
    
    $result = [];
    $result["years"] = [];
    $result["langs"] = [];
    
    foreach ($data as $key => $table) {
        $result["years"][] = $table['year'];
        $result["langs"][] = $table['lang'];
    }
    $result["years"] = array_unique($result["years"]);
    $result["langs"] = array_unique($result["langs"]);
    return $result;
}

$usrs = [];
$usrs1 = execute_query('SELECT user FROM coordinator;'); 

foreach ($usrs1 as $id => $row) {
    $usrs[] = $row['user'];
}
?>