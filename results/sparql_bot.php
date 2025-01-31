<?php

namespace Results\SparqlBot;

/*
Usage:

use function Results\SparqlBot\get_sparql_data;

*/

use function Results\ResultsHelps\print_r_it;

class SPARQLQueryDispatcher
{
    private $endpointUrl;

    public function __construct(string $endpointUrl)
    {
        $this->endpointUrl = $endpointUrl;
    }

    public function query(string $sparqlQuery): array
    {

        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => [
                    'Accept: application/sparql-results+json',
                    'User-Agent: WDQS-example PHP/' . PHP_VERSION, // TODO adjust this; see https://w.wiki/CX6
                ],
            ],
        ];
        $context = stream_context_create($opts);

        $url = $this->endpointUrl . '?query=' . urlencode($sparqlQuery);
        $response = file_get_contents($url, false, $context);
        return json_decode($response, true);
    }
}
function get_query_result($query)
{

    $endpointUrl = 'https://query.wikidata.org/sparql';

    $queryDispatcher = new SPARQLQueryDispatcher($endpointUrl);
    $queryResult = $queryDispatcher->query($query);

    var_export($queryResult);
    return $queryResult;
}

function get_sparql_data($with_qids, $code): array
{
    //---
    $data = [];
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

    //---
    // echo "<br><pre>$sparql</pre><br>";
    // ---
    print_r_it($with_qids, 'with_qids', $r = 1);
    // ---
    return $data;
}
