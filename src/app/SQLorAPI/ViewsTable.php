<?php

namespace App\SQLorAPI;

use App\SQLorAPI\BaseTable;

class ViewsTable extends BaseTable
{
    private static array $viewsCache = [];
    private static array $userViewsCache = [];
    private static array $langViewsCache = [];
    private static array $graphDataCache = [];


    private static ?self $instance = null;
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }
    public static function resetCache(): void
    {
        self::$viewsCache = [];
        self::$userViewsCache = [];
        self::$langViewsCache = [];
        self::$graphDataCache = [];
    }

    public function getViews(int|string $year, string $lang): array
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

        if ($this->service->isValid($lang)) {
            $apiParams['lang'] = $lang;
            $sqlParams[] = $lang;

            $queryComplate[] = " v.lang = ? ";
        }

        if ($this->service->isValid($year)) {
            $apiParams['year'] = $year;
            $sqlParams[] = $year;

            $queryComplate[] = " YEAR(p.pupdate) = ? ";
        }

        if (!empty($queryComplate)) {
            $query2 .= " WHERE " . implode(" AND ", $queryComplate);
        }

        $data = $this->service->superFunction($apiParams, $sqlParams, $query2);
        self::$viewsCache[$key] = $data;

        return $data;
    }

    public function getUserViews(string $user, int|string $yearY, string $langY): array
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

        if ($this->service->isValid($yearY)) {
            $query2 .= " and YEAR(p.pupdate) = ?";
            $sqlParams[] = $yearY;
        }

        $uData = $this->service->superFunction($apiParams, $sqlParams, $query2);

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

    public function getLangViews(string $mainlang, int|string $yearY): array
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

        if ($this->service->isValid($yearY)) {
            $query2 .= " and YEAR(p.pupdate) = ?";
            $sqlParams[] = $yearY;
        }

        $uData = $this->service->superFunction($apiParams, $sqlParams, $query2);

        $tableOfViews = [];

        foreach ($uData as $Key => $table) {
            $targ = $table['target'] ?? "";
            $views = $table['views'] ?? 0;
            $tableOfViews[$targ] = $views;
        }

        self::$langViewsCache[$key] = $tableOfViews;

        return $tableOfViews;
    }

    public function getGraphData(): array
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

        $uData = $this->service->superFunction($apiParams, [], $query);
        self::$graphDataCache = $uData;

        return self::$graphDataCache;
    }
}
