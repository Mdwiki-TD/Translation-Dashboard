<?php

namespace App\SQLorAPI\Funcs;

use function App\SQLorAPI\Get\superFunction;
use function App\SQLorAPI\Get\isvalid;

function getLangPagesByCat($lang, $cat)
{

    // http://localhost:9001/api.php?get=pages&lang=ar&cat=RTT
    static $data = [];

    if (!empty($data[$lang . $cat] ?? [])) {
        return $data[$lang . $cat];
    }

    $apiParams = ['get' => 'pages', 'lang' => $lang, 'cat' => $cat];

    $query = "select * from pages p where p.lang = ? and p.cat = ?";
    $params = [$lang, $cat];

    $u_data = superFunction($apiParams, $params, $query);

    $data[$lang . $cat] = $u_data;

    return $u_data;
}

function getCoordinators()
{

    static $coordinators = [];

    if (!empty($coordinators ?? [])) {
        return $coordinators;
    }

    $apiParams = ['get' => 'coordinators'];
    $query = "SELECT id, username, is_active FROM coordinators order by id";

    $u_data = superFunction($apiParams, [], $query);

    $coordinators = $u_data;

    return $coordinators;
}

function getUserPages($user_main, $year_y, $lang_y)
{

    static $data = [];

    $key = $user_main . '_' . $year_y . '_' . $lang_y;

    if (!empty($data[$key] ?? [])) {
        return $data[$key];
    }

    $apiParams = ['get' => 'pages_by_user_or_lang', 'user' => $user_main];

    $query = <<<SQL
        SELECT DISTINCT p.title, p.word, p.translate_type, p.cat, p.lang, p.user, p.target, p.date,
        p.pupdate, p.add_date, p.deleted, v.views
        FROM pages p
        LEFT JOIN views_new_all v
            ON p.target = v.target
            AND p.lang = v.lang
        where p.user = ?
    SQL;

    $sql_params = [$user_main];

    if (isvalid($year_y)) {
        $query .= " and YEAR(p.date) = ?";
        $sql_params[] = $year_y;

        $apiParams['year'] = $year_y;
    };

    if (isvalid($lang_y)) {
        $query .= " and p.lang = ?";
        $sql_params[] = $lang_y;

        $apiParams['lang'] = $lang_y;
    };

    $u_data = superFunction($apiParams, $sql_params, $query);

    $data[$key] = $u_data;

    return $u_data;
}

function getPagesWithPupdate()
{

    static $data = [];

    if (!empty($data ?? [])) {
        return $data;
    }

    $apiParams = ['get' => 'pages', 'distinct' => "1", 'select' => 'year', 'pupdate' => 'not_empty'];

    $query = "SELECT DISTINCT YEAR(pupdate) AS year FROM pages WHERE pupdate <> ''";

    $u_data = superFunction($apiParams, [], $query);

    $u_data = array_map('current', $u_data);

    $data = $u_data;

    return $u_data;
}

function getGraphData()
{

    static $graph_data = [];

    if (!empty($graph_data ?? [])) {
        return $graph_data;
    }

    $apiParams = ['get' => 'graph_data'];
    $query = <<<SQL
        SELECT LEFT(pupdate, 7) as m, COUNT(*) as c
        FROM pages
        WHERE target != ''
        GROUP BY LEFT(pupdate, 7)
        ORDER BY LEFT(pupdate, 7) ASC;
    SQL;
    $u_data = superFunction($apiParams, [], $query);

    $graph_data = $u_data;

    return $u_data;
}

function getLangPages($lang, $year_y)
{

    static $data = [];

    if (!empty($data[$lang . $year_y] ?? [])) {
        return $data[$lang . $year_y];
    }

    $apiParams = ['get' => 'pages_by_user_or_lang', 'lang' => $lang];

    $query = "select * from pages p where p.lang = ?";
    $params = [$lang];

    if (isvalid($year_y)) {
        $query .= " and YEAR(p.date) = ?";
        $params[] = $year_y;

        $apiParams['year'] = $year_y;
    };

    $u_data = superFunction($apiParams, $params, $query);

    $data[$lang . $year_y] = $u_data;

    return $u_data;
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

    $sql_params = [$user];

    if (isvalid($year_y)) {
        $query2 .= " and YEAR(p.pupdate) = ?";
        $sql_params[] = $year_y;
    }

    $u_data = superFunction($apiParams, $sql_params, $query2);

    $table_of_views = [];

    foreach ($u_data as $Key => $table) {
        $targ = $table['target'] ?? "";
        $lang = $table['lang'] ?? "";

        if (!array_key_exists($lang, $table_of_views)) {
            $table_of_views[$lang] = [];
        };

        $views = isset($table['views']) ? $table['views'] : 0;

        $table_of_views[$lang][$targ] = $views;
    };

    $data[$key] = $table_of_views;

    return $table_of_views;
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

    $sql_params = [$mainlang];

    if (isvalid($year_y)) {
        $query2 .= " and YEAR(p.pupdate) = ?";
        $sql_params[] = $year_y;
    };

    $u_data = superFunction($apiParams, $sql_params, $query2);

    $table_of_views = [];

    foreach ($u_data as $Key => $table) {
        $targ = $table['target'] ?? "";

        $views = isset($table['views']) ? $table['views'] : 0;

        $table_of_views[$targ] = $views;
    };

    $data[$key] = $table_of_views;

    return $table_of_views;
}

function getLangYears($mainlang)
{

    static $data = [];

    if (!empty($data[$mainlang] ?? [])) {
        return $data[$mainlang];
    }

    $apiParams = ['get' => 'user_lang_status', 'select' => 'year', 'lang' => $mainlang];

    $query = "SELECT DISTINCT YEAR(p.pupdate) AS year FROM pages p WHERE p.lang = ?";
    $params = [$mainlang];

    $u_data = superFunction($apiParams, $params, $query);

    $u_data = array_map('current', $u_data);

    // sort years
    rsort($u_data);

    $data[$mainlang] = $u_data;

    return $u_data;
}

function getUserYears($user)
{

    static $data = [];

    if (!empty($data[$user] ?? [])) {
        return $data[$user];
    }

    $apiParams = ['get' => 'user_status', 'select' => 'year', 'user' => $user];

    $query = "SELECT DISTINCT YEAR(p.date) AS year FROM pages p WHERE p.user = ?";

    $params = [$user];

    $u_data = superFunction($apiParams, $params, $query);

    $u_data = array_map('current', $u_data);

    // remove empty or null years
    $u_data = array_filter($u_data, function ($value) {
        return !empty($value);
    });

    // sort years
    rsort($u_data);

    $data[$user] = $u_data;

    return $u_data;
}

function getUserLangs($user)
{

    static $data = [];

    if (!empty($data[$user] ?? [])) {
        return $data[$user];
    }

    $apiParams = ['get' => 'user_status', 'select' => 'lang', 'user' => $user];

    $query = "SELECT DISTINCT p.lang FROM pages p WHERE p.user = ?";
    $params = [$user];

    $u_data = superFunction($apiParams, $params, $query);

    $u_data = array_map('current', $u_data);

    // remove empty or null years
    $u_data = array_filter($u_data, function ($value) {
        return !empty($value);
    });

    $data[$user] = $u_data;

    return $u_data;
}


function getUserCamps($user)
{

    static $data = [];

    if (!empty($data[$user] ?? [])) {
        return $data[$user];
    }

    $apiParams = ['get' => 'user_status', 'select' => 'campaign', 'user' => $user];

    $query = "SELECT DISTINCT ca.campaign
        FROM pages p
        LEFT JOIN categories ca
        ON p.cat = ca.category
        WHERE p.user = ?
    ";
    $params = [$user];

    $u_data = superFunction($apiParams, $params, $query);

    $u_data = array_map('current', $u_data);

    // remove empty or null years
    $u_data = array_filter($u_data, function ($value) {
        return !empty($value);
    });

    $data[$user] = $u_data;

    return $u_data;
}
