<?php
header('Content-Type: application/json');
header('Content-Encoding: UTF-8');

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

if (empty($cat) && !empty($camp)) {
    $cat = TablesSql::$s_camp_to_cat[$camp] ?? '';
}

$cat   = $cat ?? "RTTHearing";
$camp  = $camp ?? "Hearing";

$results = get_results($cat, $camp, $depth, $code, $filter_sparql);
$results = get_results_new($cat, $camp, $depth, $code, $filter_sparql);

// $results['inprocess'] = normalizeItems($results['inprocess']);
// $results['missing'] = normalizeItems($results['missing']);
// $results['exists'] = normalizeItems($results['exists']);

echo json_encode($results, JSON_PRETTY_PRINT);
