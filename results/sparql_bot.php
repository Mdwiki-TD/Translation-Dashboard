<?php

namespace Results\SparqlBot;

/*
Usage:

use function Results\SparqlBot\get_sparql_data;
use function Results\SparqlBot\get_sparql_data_not_exists;

*/

use function Results\ResultsHelps\print_r_it;
use function Actions\Functions\test_print;
use function SQLorAPI\GetDataTab\get_td_or_sql_qids;


class SPARQLQueryDispatcher
{
    public $endpointUrl;
    public $user_agent;

    public function __construct()
    {
        $this->endpointUrl = 'https://query.wikidata.org/sparql';
        $this->user_agent = "User-Agent: WikiProjectMed Translation Dashboard/1.0 (https://mdwiki.toolforge.org/; tools.mdwiki@toolforge.org) PHP/" . PHP_VERSION;
    }

    function get_url(string $url): string
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $output = curl_exec($ch);

        curl_close($ch);
        return $output;
    }
    public function query_old($sparqlQuery): array
    {

        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => [
                    'Accept: application/sparql-results+json',
                    $this->user_agent,
                ],
            ],
        ];
        $context = stream_context_create($opts);

        $url = $this->endpointUrl . '?query=' . urlencode($sparqlQuery) . '&format=json';
        $response = file_get_contents($url, false, $context);
        return json_decode($response, true) ?? [];
    }

    public function query($sparqlQuery): array
    {
        $url = $this->endpointUrl . '?query=' . urlencode($sparqlQuery) . '&format=json';
        $response = $this->get_url($url);

        // print_r($response);

        return json_decode($response, true) ?? [];
    }
}

function get_query_result($query)
{
    $queryDispatcher = new SPARQLQueryDispatcher();
    $queryResult = $queryDispatcher->query($query);
    $result = $queryResult['results']['bindings'];
    // ---
    $vars = $queryResult['head']['vars'];
    // ---
    $list = [];
    // ---
    foreach ($result as $item) {
        $d = [];
        // $d = array_map(function ($x) {return $x['value'];}, $item);
        foreach ($vars as $var) {
            $d[$var] = $item[$var]['value'] ?? "";
        }
        $d["item"] = str_replace("http://www.wikidata.org/entity/", "", $d["item"]);
        $list[] = $d;
    }
    // ---
    return $list;
}
function get_sparql_data($with_qids, $code): array
{
    //---
    $wd_values = "wd:" . implode(' wd:', array_values($with_qids));
    //---
    $sparql = "
        SELECT ?item ?article WHERE {
            VALUES ?item { $wd_values }
            OPTIONAL {
                ?sitelink schema:about ?item;
                    schema:isPartOf <https://$code.wikipedia.org/>;
                    schema:name ?article.
            }
        }
    ";
    //---
    $result = get_query_result($sparql);
    //---
    print_r_it($result, 'sparql result');
    // ---
    $exists = [];
    $missing = [];
    //---
    $qids_to_title = [];
    //---
    foreach ($with_qids as $title => $qid) {
        $qids_to_title[$qid] = $title;
    };
    //---
    foreach ($result as $item) {
        if ($item['article'] != "") {
            $exists[] = $item['article'];
        } else {
            $article = $qids_to_title[$item['item']];
            $missing[] = $article;
        }
    }
    //---
    return [
        "exists" => $exists,
        "missing" => $missing
    ];
}

function get_sparql_data_not_exists($with_qids, $code): array
{
    //---
    $qids_to_title = [];
    //---
    foreach ($with_qids as $title => $qid) {
        $qids_to_title[$qid] = $title;
    };
    //---
    $no_article = [];
    $missing = [];
    //---
    $chunks = array_chunk($with_qids, 100);

    foreach ($chunks as $chunk) {
        $wd_values = "wd:" . implode(' wd:', array_values($chunk));
        //---
        $sparql = "
            SELECT ?item WHERE {
                VALUES ?item { $wd_values }
                FILTER NOT EXISTS {
                    ?sitelink schema:about ?item;
                        schema:isPartOf <https://$code.wikipedia.org/>;
                        schema:name ?article.
                }
            }
        ";
        //---
        $result = get_query_result($sparql);
        //---
        if (count($result) == 0) {
            $missing = array_merge($missing, array_keys($chunk));
            continue;
        }
        //---
        foreach ($result as $item) {
            $article = $qids_to_title[$item['item']] ?? 0;
            if ($article != 0) {
                $missing[] = $article;
            } else {
                $no_article[] = $item['item'];
            }
        }
    }
    //---
    print_r_it($result, 'sparql result');
    print_r_it($missing, 'sparql missing');
    print_r_it($no_article, 'no_article');
    // ---
    return $missing;
}

function get_qids($list)
{
    //---
    $sql_qids = get_td_or_sql_qids();
    //---
    $with_qids = [];
    $no_qids = [];
    // ---
    foreach ($list as $member) {
        $qid = $sql_qids[$member] ?? 0;
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

function check_missing($missing, $code): array
{
    //---
    $qids_tab = get_qids($missing);
    //---
    $with_qids = $qids_tab['with_qids'];
    $no_qids = $qids_tab['no_qids'];
    //---
    // print_r($no_qids);
    //---
    $sparql_missing = get_sparql_data_not_exists($with_qids, $code) ?? [];
    //---
    // if (count($sparql_missing) == 0) { return $missing; }
    //---
    $sparql_missing = array_merge($sparql_missing, $no_qids);
    //---
    if (count($sparql_missing) == count($missing)) {
        test_print("sparql_missing == missing");
        return $sparql_missing;
    }
    // ---
    // find the difference
    $diff = array_diff($missing, $sparql_missing);
    // ---
    if (count($diff) > 10) {
        return $missing;
    };
    // ---
    print_r_it($sparql_missing, 'sparql_missing', $r = 1);
    print_r_it($diff, 'diff', $r = 1);
    // ---
    test_print("check_missing diff: " . count($diff));
    test_print($diff);
    // ---
    return $sparql_missing;
}
