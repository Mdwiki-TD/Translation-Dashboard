<?php

namespace App\SQLorAPI;

use App\SQLorAPI\ApiOrSqlService;

class PagesTable
{
    private static array $pagesCache = [];
    private static array $pupdateCache = [];
    private static array $countPagesCache = [];
    private static array $countPagesCacheNotEmpty = [];
    private static array $userYearsCache = [];
    private static array $userLangsCache = [];
    private static array $userCampsCache = [];
    private static array $langYearsCache = [];

    private ApiOrSqlService $service;
    public function __construct(?ApiOrSqlService $service = null)
    {
        $this->service = $service ?? new ApiOrSqlService();
    }

    public static function resetCache(): void
    {
        self::$pagesCache = [];
        self::$pupdateCache = [];
        self::$countPagesCache = [];
        self::$countPagesCacheNotEmpty = [];
        self::$userYearsCache = [];
        self::$userLangsCache = [];
        self::$userCampsCache = [];
        self::$langYearsCache = [];
    }

    public function getLangPagesByCat(mixed $lang, mixed $cat): array
    {
        // http://localhost:9001/api.php?get=pages&lang=ar&cat=RTT
        $key = (string)$lang . (string)$cat;
        if (!empty(self::$pagesCache[$key] ?? [])) {
            return self::$pagesCache[$key];
        }

        $apiParams = ['get' => 'pages', 'lang' => $lang, 'cat' => $cat];
        $query = "select * from pages p where p.lang = ? and p.cat = ?";
        $params = [$lang, $cat];

        $uData = $this->service->superFunction($apiParams, $params, $query);
        self::$pagesCache[$key] = $uData;

        return $uData;
    }

    public function getUserPages(mixed $userMain, mixed $yearY, mixed $langY): array
    {
        $key = $userMain . '_' . $yearY . '_' . $langY;
        if (!empty(self::$pagesCache[$key] ?? [])) {
            return self::$pagesCache[$key];
        }

        $apiParams = ['get' => 'pages_by_user_or_lang', 'user' => $userMain];

        $query = <<<SQL
            SELECT DISTINCT p.title, p.word, p.translate_type, p.cat, p.lang, p.user, p.target, p.date,
            p.pupdate, p.add_date, p.deleted, v.views
            FROM pages p
            LEFT JOIN views_new_all v
                ON p.target = v.target
                AND p.lang = v.lang
            where p.user = ?
        SQL;

        $sqlParams = [$userMain];

        if (ApiOrSqlService::isValid($yearY)) {
            $query .= " and YEAR(p.date) = ?";
            $sqlParams[] = $yearY;
            $apiParams['year'] = $yearY;
        }

        if (ApiOrSqlService::isValid($langY)) {
            $query .= " and p.lang = ?";
            $sqlParams[] = $langY;
            $apiParams['lang'] = $langY;
        }

        $uData = $this->service->superFunction($apiParams, $sqlParams, $query);
        self::$pagesCache[$key] = $uData;

        return $uData;
    }

    public function getPagesWithPupdate(): array
    {
        if (!empty(self::$pupdateCache)) {
            return self::$pupdateCache;
        }

        $apiParams = ['get' => 'pages', 'distinct' => "1", 'select' => 'year', 'pupdate' => 'not_empty'];
        $query = "SELECT DISTINCT YEAR(pupdate) AS year FROM pages WHERE pupdate <> ''";

        $uData = $this->service->superFunction($apiParams, [], $query);
        $uData = array_map('current', $uData);

        self::$pupdateCache = $uData;

        return self::$pupdateCache;
    }

    public function getLangPages(mixed $lang, mixed $yearY): array
    {
        $key = (string)$lang . (string)$yearY;
        if (!empty(self::$pagesCache[$key] ?? [])) {
            return self::$pagesCache[$key];
        }

        $apiParams = ['get' => 'pages_by_user_or_lang', 'lang' => $lang];
        $query = "select * from pages p where p.lang = ?";
        $params = [$lang];

        if (ApiOrSqlService::isValid($yearY)) {
            $query .= " and YEAR(p.date) = ?";
            $params[] = $yearY;
            $apiParams['year'] = $yearY;
        }

        $uData = $this->service->superFunction($apiParams, $params, $query);
        self::$pagesCache[$key] = $uData;

        return $uData;
    }

    public function getLangYears(mixed $mainlang): array
    {
        $key = (string)$mainlang;
        if (!empty(self::$langYearsCache[$key] ?? [])) {
            return self::$langYearsCache[$key];
        }

        $apiParams = ['get' => 'user_lang_status', 'select' => 'year', 'lang' => $mainlang];

        $query = "SELECT DISTINCT YEAR(p.pupdate) AS year FROM pages p WHERE p.lang = ?";
        $params = [$mainlang];

        $uData = $this->service->superFunction($apiParams, $params, $query);

        $uData = array_map('current', $uData);

        // sort years
        rsort($uData);

        self::$langYearsCache[$key] = $uData;

        return self::$langYearsCache[$key];
    }

    public function getUserYears(mixed $user): array
    {
        $key = (string)$user;
        if (!empty(self::$userYearsCache[$key] ?? [])) {
            return self::$userYearsCache[$key];
        }

        $apiParams = ['get' => 'user_status', 'select' => 'year', 'user' => $user];
        $query = "SELECT DISTINCT YEAR(p.date) AS year FROM pages p WHERE p.user = ?";
        $params = [$user];

        $uData = $this->service->superFunction($apiParams, $params, $query);
        $uData = array_map('current', $uData);

        // remove empty or null years
        $uData = array_filter($uData, function ($value) {
            return !empty($value);
        });

        // sort years
        rsort($uData);

        self::$userYearsCache[$key] = $uData;

        return self::$userYearsCache[$key];
    }

    public function getUserLangs(mixed $user): array
    {
        $key = (string)$user;
        if (!empty(self::$userLangsCache[$key] ?? [])) {
            return self::$userLangsCache[$key];
        }

        $apiParams = ['get' => 'user_status', 'select' => 'lang', 'user' => $user];
        $query = "SELECT DISTINCT p.lang FROM pages p WHERE p.user = ?";
        $params = [$user];

        $uData = $this->service->superFunction($apiParams, $params, $query);
        $uData = array_map('current', $uData);

        // remove empty or null years
        $uData = array_filter($uData, function ($value) {
            return !empty($value);
        });

        self::$userLangsCache[$key] = $uData;

        return self::$userLangsCache[$key];
    }

    public function getUserCamps(mixed $user): array
    {
        $key = (string)$user;
        if (!empty(self::$userCampsCache[$key] ?? [])) {
            return self::$userCampsCache[$key];
        }

        $apiParams = ['get' => 'user_status', 'select' => 'campaign', 'user' => $user];
        $query = "SELECT DISTINCT ca.campaign
            FROM pages p
            LEFT JOIN categories ca
            ON p.cat = ca.category
            WHERE p.user = ?
        ";
        $params = [$user];

        $uData = $this->service->superFunction($apiParams, $params, $query);
        $uData = array_map('current', $uData);

        // remove empty or null years
        $uData = array_filter($uData, function ($value) {
            return !empty($value);
        });

        self::$userCampsCache[$key] = $uData;

        return self::$userCampsCache[$key];
    }

    public function getCountPages(): array
    {
        if (!empty(self::$countPagesCache)) {
            return self::$countPagesCache;
        }

        $apiParams = ['get' => 'count_pages'];
        $query = "SELECT DISTINCT user, count(target) as count from pages group by user order by count desc";

        $data = $this->service->superFunction($apiParams, [], $query);
        $data = array_column($data, 'count', 'user');

        arsort($data);

        self::$countPagesCache = $data;

        return self::$countPagesCache;
    }
    public function getCountPagesNotEmpty(): array
    {
        if (!empty(self::$countPagesCacheNotEmpty)) {
            return self::$countPagesCacheNotEmpty;
        }

        $apiParams = ['get' => 'count_pages', 'target' => 'not_empty'];
        $query = "SELECT DISTINCT user, count(target) as count from pages where target != '' group by user order by count desc";

        $data = $this->service->superFunction($apiParams, [], $query);
        $data = array_column($data, 'count', 'user');

        arsort($data);

        self::$countPagesCacheNotEmpty = $data;

        return self::$countPagesCacheNotEmpty;
    }
}


function getCountPages()
{
    return (new PagesTable())->getCountPages();
}

function getLangPagesByCat($lang, $cat)
{
    return (new PagesTable())->getLangPagesByCat($lang, $cat);
}

function getUserPages($userMain, $year_y, $lang_y)
{
    return (new PagesTable())->getUserPages($userMain, $year_y, $lang_y);
}

function getPagesWithPupdate()
{
    return (new PagesTable())->getPagesWithPupdate();
}

function getLangPages($lang, $year_y)
{
    return (new PagesTable())->getLangPages($lang, $year_y);
}

function getLangYears($mainlang)
{
    return (new PagesTable())->getLangYears($mainlang);
}

function getUserYears($user)
{
    return (new PagesTable())->getUserYears($user);
}

function getUserLangs($user)
{
    return (new PagesTable())->getUserLangs($user);
}

function getUserCamps($user)
{
    return (new PagesTable())->getUserCamps($user);
}
