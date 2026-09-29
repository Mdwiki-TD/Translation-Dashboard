<?php

namespace App\SQLorAPI;

use App\SQLorAPI\Get\ApiOrSqlService;

class ViewsTable
{
    private static array $viewsCache = [];
    private static array $userViewsCache = [];
    private static array $langViewsCache = [];
    private static array $graphDataCache = [];

    public static function resetCache(): void
    {
        self::$viewsCache = [];
        self::$userViewsCache = [];
        self::$langViewsCache = [];
        self::$graphDataCache = [];
    }

    public static function getViews(mixed $year, mixed $lang): array
    {
        $key = (string)$year . (string)$lang;
        if (!empty(self::$viewsCache[$key] ?? [])) {
            return self::$viewsCache[$key];
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

        if (ApiOrSqlService::isValid($lang)) {
            $apiParams['lang'] = $lang;
            $sqlParams[] = $lang;

            $queryComplate[] = " v.lang = ? ";
        }

        if (ApiOrSqlService::isValid($year)) {
            $apiParams['year'] = $year;
            $sqlParams[] = $year;

            $queryComplate[] = " YEAR(p.pupdate) = ? ";
        }

        if (!empty($queryComplate)) {
            $query2 .= " WHERE " . implode(" AND ", $queryComplate);
        }

        $data = ApiOrSqlService::superFunction($apiParams, $sqlParams, $query2);
        self::$viewsCache[$key] = $data;

        return $data;
    }

    public static function getUserViews(mixed $user, mixed $yearY, mixed $langY): array
    {
        $key = 'user_views_' . $user . '_' . $yearY . '_' . $langY;
        if (!empty(self::$userViewsCache[$key] ?? [])) {
            return self::$userViewsCache[$key];
        }

        $apiParams = ['get' => 'user_views2', 'lang' => $langY, 'user' => $user, 'year' => $yearY];

        $query2 = <<<SQL
            SELECT v.target, v.lang, v.views
            FROM views_new_all v
            JOIN pages p
                ON p.target = v.target
                AND p.lang = v.lang
            WHERE p.user = ?
        SQL;

        $sqlParams = [$user];

        if (ApiOrSqlService::isValid($yearY)) {
            $query2 .= " and YEAR(p.pupdate) = ?";
            $sqlParams[] = $yearY;
        }

        $uData = ApiOrSqlService::superFunction($apiParams, $sqlParams, $query2);

        $tableOfViews = [];

        foreach ($uData as $Key => $table) {
            $targ = $table['target'] ?? "";
            $lang = $table['lang'] ?? "";

            if (!array_key_exists($lang, $tableOfViews)) {
                $tableOfViews[$lang] = [];
            }

            $views = $table['views'] ?? 0;
            $tableOfViews[$lang][$targ] = $views;
        }

        self::$userViewsCache[$key] = $tableOfViews;

        return $tableOfViews;
    }

    public static function getLangViews(mixed $mainlang, mixed $yearY): array
    {
        $key = 'lang_views_' . $mainlang . '_' . $yearY;
        if (!empty(self::$langViewsCache[$key] ?? [])) {
            return self::$langViewsCache[$key];
        }

        $apiParams = ['get' => 'lang_views2', 'lang' => $mainlang, 'year' => $yearY];

        $query2 = <<<SQL
            SELECT v.target, v.lang, v.views
            FROM views_new_all v
            JOIN pages p
                ON p.target = v.target
                AND p.lang = v.lang
            WHERE p.lang = ?
        SQL;

        $sqlParams = [$mainlang];

        if (ApiOrSqlService::isValid($yearY)) {
            $query2 .= " and YEAR(p.pupdate) = ?";
            $sqlParams[] = $yearY;
        }

        $uData = ApiOrSqlService::superFunction($apiParams, $sqlParams, $query2);

        $tableOfViews = [];

        foreach ($uData as $Key => $table) {
            $targ = $table['target'] ?? "";
            $views = $table['views'] ?? 0;
            $tableOfViews[$targ] = $views;
        }

        self::$langViewsCache[$key] = $tableOfViews;

        return $tableOfViews;
    }

    public static function getGraphData(): array
    {
        if (!empty(self::$graphDataCache)) {
            return self::$graphDataCache;
        }

        $apiParams = ['get' => 'graph_data'];
        $query = <<<SQL
            SELECT LEFT(pupdate, 7) as m, COUNT(*) as c
            FROM pages
            WHERE target != ''
            GROUP BY LEFT(pupdate, 7)
            ORDER BY LEFT(pupdate, 7) ASC;
        SQL;

        $uData = ApiOrSqlService::superFunction($apiParams, [], $query);
        self::$graphDataCache = $uData;

        return self::$graphDataCache;
    }
}

function getViews($year, $lang)
{
    return ViewsTable::getViews($year, $lang);
}

function getGraphData()
{
    return ViewsTable::getGraphData();
}

function getUserViews($user, $year_y, $lang_y)
{
    return ViewsTable::getUserViews($user, $year_y, $lang_y);
}

function getLangViews($mainlang, $year_y)
{
    return ViewsTable::getLangViews($mainlang, $year_y);
}

