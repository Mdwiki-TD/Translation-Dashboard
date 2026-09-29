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

function getUserViews($user, $year_y, $lang_y)
{

    static $data = [];

    $key = 'user_views_' . $user . '_' . $year_y . '_' . $lang_y;

    if (!empty($data[$key] ?? [])) {
        return $data[$key];
    }

    $apiParams = ['get' => 'user_views2', 'lang' => $lang_y, 'user' => $user, 'year' => $year_y];

    $query2 = <<<SQL
        SELECT v.target, v.lang, v.views
        FROM views_new_all v
        JOIN pages p
            ON p.target = v.target
            AND p.lang = v.lang
        WHERE p.user = ?
    SQL;

    $sqlParams = [$user];

    if (isValid($year_y)) {
        $query2 .= " and YEAR(p.pupdate) = ?";
        $sqlParams[] = $year_y;
    }

    $uData = superFunction($apiParams, $sqlParams, $query2);

    $tableOfViews = [];

    foreach ($uData as $Key => $table) {
        $targ = $table['target'] ?? "";
        $lang = $table['lang'] ?? "";

        if (!array_key_exists($lang, $tableOfViews)) {
            $tableOfViews[$lang] = [];
        };

        $views = isset($table['views']) ? $table['views'] : 0;

        $tableOfViews[$lang][$targ] = $views;
    };

    $data[$key] = $tableOfViews;

    return $tableOfViews;
}

function getLangViews($mainlang, $year_y)
{

    static $data = [];

    $key = 'lang_views_' . $mainlang . '_' . $year_y;

    if (!empty($data[$key] ?? [])) {
        return $data[$key];
    }

    $apiParams = ['get' => 'lang_views2', 'lang' => $mainlang, 'year' => $year_y];

    $query2 = <<<SQL
        SELECT v.target, v.lang, v.views
        FROM views_new_all v
        JOIN pages p
            ON p.target = v.target
            AND p.lang = v.lang
        WHERE p.lang = ?
    SQL;

    $sqlParams = [$mainlang];

    if (isValid($year_y)) {
        $query2 .= " and YEAR(p.pupdate) = ?";
        $sqlParams[] = $year_y;
    };

    $uData = superFunction($apiParams, $sqlParams, $query2);

    $tableOfViews = [];

    foreach ($uData as $Key => $table) {
        $targ = $table['target'] ?? "";

        $views = isset($table['views']) ? $table['views'] : 0;

        $tableOfViews[$targ] = $views;
    };

    $data[$key] = $tableOfViews;

    return $tableOfViews;
}

function getGraphData()
{

    static $graphData = [];

    if (!empty($graphData ?? [])) {
        return $graphData;
    }

    $apiParams = ['get' => 'graph_data'];
    $query = <<<SQL
        SELECT LEFT(pupdate, 7) as m, COUNT(*) as c
        FROM pages
        WHERE target != ''
        GROUP BY LEFT(pupdate, 7)
        ORDER BY LEFT(pupdate, 7) ASC;
    SQL;
    $uData = superFunction($apiParams, [], $query);

    $graphData = $uData;

    return $uData;
}
