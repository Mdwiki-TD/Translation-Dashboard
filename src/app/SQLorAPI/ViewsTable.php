<?php

namespace App\SQLorAPI\GetDataTab;

use function App\SQLorAPI\Get\superFunction;
use function App\SQLorAPI\Get\isValid;


function getViews($year, $lang)
{

    static $cache = [];

    if (!empty($cache[$year . $lang] ?? [])) {
        return $cache[$year . $lang];
    }

    $apiParams = ['get' => 'views_new'];

    $query2 = <<<SQL
        SELECT p.title, v.target, v.lang, v.views
        FROM views_new_all v
        LEFT JOIN pages p
            ON p.target = v.target
            AND p.lang = v.lang
    SQL;

    $queryComplate = [];

    $sqlParams = [];

    if (isValid($lang)) {
        $apiParams['lang'] = $lang;
        $sqlParams[] = $lang;

        $queryComplate[] = " v.lang = ? ";
    }

    if (isValid($year)) {
        $apiParams['year'] = $year;
        $sqlParams[] = $year;

        $queryComplate[] = " YEAR(p.pupdate) = ? ";
    }

    if (!empty($queryComplate)) {
        $query2 .= " WHERE " . implode(" AND ", $queryComplate);
    }

    $data = superFunction($apiParams, $sqlParams, $query2);

    $cache[$year . $lang] = $data;

    return $data;
}
