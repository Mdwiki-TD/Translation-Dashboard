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
            echo $s;
        } else {
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

$usrs = [];
$usrs1 = execute_query('SELECT user FROM coordinator;'); 

foreach ($usrs1 as $id => $row) {
    $usrs[] = $row['user'];
}
?>