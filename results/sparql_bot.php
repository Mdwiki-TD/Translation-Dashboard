<?php

namespace Results\SparqlBot;

/*
Usage:

use function Results\SparqlBot\get_sparql_data;
use function Results\SparqlBot\get_sparql_data_exists;

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
function get_sparql_data_exists($with_qids, $code): array
{
    //---
    $qids_to_title = [];
    //---
    foreach ($with_qids as $title => $qid) {
        $qids_to_title[$qid] = $title;
    };
    //---
    $no_article = [];
    $EXISTS = [];
    //---
    $chunks = array_chunk($with_qids, 100);

    foreach ($chunks as $chunk) {
        $wd_values = "wd:" . implode(' wd:', array_values($chunk));
        //---
        $sparql = "
            SELECT ?item WHERE {
                VALUES ?item { $wd_values }
                FILTER EXISTS {
                    ?sitelink schema:about ?item;
                        schema:isPartOf <https://$code.wikipedia.org/>;
                        schema:name ?article.
                }
            }
        ";
        //---
        $result = get_query_result($sparql);
        //---
        foreach ($result as $item) {
            $article = $qids_to_title[$item['item']] ?? 0;
            if ($article != 0) {
                $EXISTS[] = $article;
            } else {
                $no_article[] = $item['item'];
            }
        }
    }
    //---
    print_r_it($result, 'sparql result');
    print_r_it($EXISTS, 'sparql EXISTS');
    print_r_it($no_article, 'no_article');
    // ---
    return $EXISTS;
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

function filter_existing_out($missing, $code): array
{
    //---
    $missing2 = [];
    //---
    foreach ($missing as $_ => $title) {
        $missing2[] = $title;
    }
    //---
    $missing = $missing2;
    //---
    print_r_it($missing2, 'missing2');
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
