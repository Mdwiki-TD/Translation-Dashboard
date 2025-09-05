<?php

namespace Results\SparqlBot;

/*
Usage:

use function Results\SparqlBot\get_sparql_data;
use function Results\SparqlBot\get_sparql_data_exists;

*/

use function Results\ResultsHelps\print_r_it;
use function Actions\TestPrint\test_print;
use function SQLorAPI\GetDataTab\get_td_or_sql_qids;
use function Results\SPARQL_Dispatcher\get_query_result;

function sparql_query_result($with_qids, $code): array
{
    //---
    $table = [];
    //---
    $chunks = array_chunk($with_qids, 150);
    //---
    $time_start = microtime(true);
    //---
    foreach ($chunks as $chunk) {
        $wd_values = "wd:" . implode(' wd:', array_values($chunk));
        //---
        $sparql = "
            SELECT ?item ?article WHERE {
                VALUES ?item {
                    $wd_values
                }
                ?sitelink schema:about ?item;
                    schema:isPartOf <https://$code.wikipedia.org/>;
                    schema:name ?article.
                }
        ";
        //---
        // test_print("<br>" . htmlspecialchars($sparql) . "<br>");
        //---
        $result = get_query_result($sparql);
        //---
        foreach ($result as $item) {
            $table[] = $item['item'];
        }
    }
    //---
    print_r_it($table, 'sparql_query_result', $d = 1);
    // ---
    $execution_time = (microtime(true) - $time_start);
    test_print("<br> >>>>> sparql_query_result Total Execution Time: " . $execution_time . " Seconds<br>");
    // ---
    return $table;
}

function get_sparql_data_exists($with_qids, $code): array
{
    //---
    print_r_it($with_qids, 'with_qids', $r = 1);
    // ---
    $no_article = [];
    $EXISTS = [];
    //---
    $qids_exists = sparql_query_result($with_qids, $code);
    //---
    foreach ($with_qids as $title => $qid) {
        if (in_array($qid, $qids_exists)) {
            $EXISTS[] = $title;
        }
    }
    //---
    print_r_it($EXISTS, 'sparql EXISTS');
    print_r_it($no_article, 'no_article', $d = 1);
    // ---
    return $EXISTS;
}

function get_qids($list)
{
    //---
    $sq_qids = get_td_or_sql_qids();
    //---
    $with_qids = [];
    $no_qids = [];
    // ---
    foreach ($list as $member) {
        $qid = $sq_qids[$member] ?? 0;
        if ($qid) {
            $with_qids[$member] = $qid;
        } else {
            $no_qids[] = $member;
        }
    }
    // ---
    print_r_it($with_qids, "with_qids");
    print_r_it($no_qids, "no_qids");
    // ---
    return [
        "with_qids" => $with_qids,
        "no_qids" => $no_qids,
    ];
}

function filter_existing_out($missing, $code): array
{
    //---
    $missing2 = [];
    //---
    foreach ($missing as $_ => $title) $missing2[] = $title;
    //---
    $missing = $missing2;
    //---
    print_r_it($missing2, '<br>missing2');
    //---
    $qids_tab = get_qids($missing);
    //---
    $with_qids = $qids_tab['with_qids'];
    //---
    $sparql_exists = get_sparql_data_exists($with_qids, $code) ?? [];
    //---
    print_r_it($sparql_exists, 'sparql_exists', $r = 1);
    // ---
    if (count($sparql_exists) == 0) {
        return $missing;
    }
    //---
    $new_missings = [];
    //---
    // Filter out titles that exist in $sparql_exists from $missing
    foreach ($missing as $title) {
        if (!in_array($title, $sparql_exists)) {
            $new_missings[] = $title;
        }
    };
    // ---
    print_r_it($new_missings, 'new_missings', $d = 1);
    // ---
    test_print("filter_existing_out sparql_exists count: " . count($sparql_exists));
    // ---
    return $new_missings;
}
