<?php

namespace Results\SPARQL_Dispatcher;

/*
Usage:

use function Results\SPARQL_Dispatcher\get_query_result;
use function Results\SPARQL_Dispatcher\SPARQLQueryDispatcher;

*/

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

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true, // لمنع الطباعة
            CURLOPT_USERAGENT => $this->user_agent,
            CURLOPT_TIMEOUT => 10, // المهلة القصوى للاتصال
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);

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
    $result = $queryResult['results']['bindings'] ?? [];
    // ---
    $vars = $queryResult['head']['vars'] ?? [];
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
