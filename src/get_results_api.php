<?php
header('Content-Type: application/json; charset=UTF-8');


$time_start = microtime(true);

use function Results\GetResults\get_results;
use function Results\GetResults\get_results_new;

include_once __DIR__ . '/include_all.php';

$cat   = $_GET['cat'] ?? "";
$cat2  = $_GET['cat2'] ?? "";
$camp  = $_GET['camp'] ?? "";
$depth = $_GET['depth'] ?? "1";
$code  = $_GET['code'] ?? "gg";

$filter_sparql  = $_GET['filter_sparql'] ?? true;
$new  = $_GET['new'] ?? false;

$example = '';

if (empty($cat) && empty($camp) && empty($code)) {
    $example = "?cat=RTTHearing&camp=Hearing&depth=1&code=ar";
}

$results = [];

if ((!empty($cat) || !empty($camp)) && !empty($code)) {
    if ($new) {
        $results = get_results_new($cat, $camp, $depth, $code, $filter_sparql, $cat2);
    } else {
        $results = get_results($cat, $camp, $depth, $code, $filter_sparql, $cat2);
    }
}

$tab = [
    "execution_time" => (microtime(true) - $time_start),
    "results" => $results
];
if (!empty($example)) {
    $tab['example'] = $example;
}

echo json_encode($tab, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
