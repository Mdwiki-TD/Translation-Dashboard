<?php
header('Content-Type: application/json');
header('Content-Encoding: UTF-8');

//---
$time_start = microtime(true);
//---
$_REQUEST['test'] = "x";
$_COOKIE['test'] = "x";
$_GET['test'] = "x";

include_once __DIR__ . '/include_all.php';

use Tables\SqlTables\TablesSql;
use function Results\GetResults\get_results;
use function Results\GetResults\get_results_new;
use function Results\ResultsTable\normalizeItems;

$cat   = $_GET['cat'] ?? "";
$camp  = $_GET['camp'] ?? "";
$depth = $_GET['depth'] ?? "1";
$code  = $_GET['code'] ?? "gg";
$filter_sparql  = $_GET['filter_sparql'] ?? true;
$new  = $_GET['new'] ?? false;

if (empty($cat) && !empty($camp)) {
    $cat = TablesSql::$s_camp_to_cat[$camp] ?? '';
}

if (empty($cat)) {
    $cat = "RTTHearing";
}
if (empty($camp)) {
    $camp = "Hearing";
}

if ($new) {
    $results = get_results_new($cat, $camp, $depth, $code, $filter_sparql);
} else {
    $results = get_results($cat, $camp, $depth, $code, $filter_sparql);
}

// $results['inprocess'] = normalizeItems($results['inprocess']);
// $results['missing'] = normalizeItems($results['missing']);
// $results['exists'] = normalizeItems($results['exists']);

$tab = [
    "execution_time" => (microtime(true) - $time_start),
    "results" => $results
];
// ---
echo json_encode($tab, JSON_PRETTY_PRINT);
