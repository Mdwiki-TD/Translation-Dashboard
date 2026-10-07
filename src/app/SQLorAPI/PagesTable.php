<?php

namespace App\SQLorAPI;

use App\SQLorAPI\BaseTable;

class PagesTable extends BaseTable
{
    private static array $langPagesCache = [];
    private static array $pagesCache = [];
    private static array $pupdateCache = [];
    private static array $countPagesCache = [];
    private static array $countPagesCacheNotEmpty = [];
    private static array $langYearsCache = [];


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
        self::$langPagesCache = [];
        self::$pagesCache = [];
        self::$pupdateCache = [];
        self::$countPagesCache = [];
        self::$countPagesCacheNotEmpty = [];
        self::$langYearsCache = [];
    }

    public function getLangPagesByCat(string $lang, string $cat): array
    {
        // http://localhost:9001/api.php?get=pages&lang=ar&cat=RTT
        $key = $lang . $cat;
        if (!empty(self::$langPagesCache[$key] ?? [])) {
            return self::$langPagesCache[$key];
        }

        $apiParams = ['get' => 'pages', 'lang' => $lang, 'cat' => $cat];
        $query = "select * from pages p where p.lang = ? and p.cat = ?";
        $params = [$lang, $cat];

        $uData = $this->service->superFunction($apiParams, $params, $query);
        self::$langPagesCache[$key] = $uData;

        return $uData;
    }

    public function getUserPages(string $userMain, int|string $yearY, string $langY): array
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

        if ($this->service->isValid($yearY)) {
            $query .= " and YEAR(p.date) = ?";
            $sqlParams[] = $yearY;
            $apiParams['year'] = $yearY;
        }

        if ($this->service->isValid($langY)) {
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

    public function getLangPages(string $lang, int|string $yearY): array
    {
        $key = (string)$lang . (string)$yearY;
        if (!empty(self::$pagesCache[$key] ?? [])) {
            return self::$pagesCache[$key];
        }

        $apiParams = ['get' => 'pages_by_user_or_lang', 'lang' => $lang];
        $query = "select * from pages p where p.lang = ?";
        $params = [$lang];

        if ($this->service->isValid($yearY)) {
            $query .= " and YEAR(p.date) = ?";
            $params[] = $yearY;
            $apiParams['year'] = $yearY;
        }

        $uData = $this->service->superFunction($apiParams, $params, $query);
        self::$pagesCache[$key] = $uData;

        return $uData;
    }

    public function getLangYears(string $mainlang): array
    {
        $key = (string)$mainlang;
        if (!empty(self::$langYearsCache[$key] ?? [])) {
            return self::$langYearsCache[$key];
        }

        $apiParams = ['get' => 'get_lang_years', 'lang' => $mainlang];

        $query = "SELECT DISTINCT YEAR(p.pupdate) AS year FROM pages p WHERE p.lang = ?";
        $params = [$mainlang];

        $uData = $this->service->superFunction($apiParams, $params, $query);

        $uData = array_map('current', $uData);

        // sort years
        rsort($uData);

        self::$langYearsCache[$key] = $uData;

        return self::$langYearsCache[$key];
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

    public function getPageUserNotInUsers(): array
    {

        static $users = [];

        if (!empty($users ?? [])) {
            return $users;
        }

        $sqlParams = [];
        $apiParams = ['get' => 'pages', 'distinct' => 1, 'select' => 'user'];
        $query = <<<SQL
            select DISTINCT p.user from pages AS p WHERE NOT EXISTS ( SELECT 1 FROM users AS u WHERE p.user = u.username )
        SQL;

        $data = $this->service->superFunction($apiParams, $sqlParams, $query);

        $data = array_column($data, 'user');

        $users = $data;

        return $data;
    }

    public function getPagesLangs(): array
    {

        static $pagesLangs = [];

        if (!empty($pagesLangs ?? [])) {
            return $pagesLangs;
        }

        $sqlParams = [];
        $apiParams = ['get' => 'pages', 'distinct' => "1", 'select' => 'lang', 'lang' => 'not_empty'];
        $query = "SELECT DISTINCT lang FROM pages where (lang != '' and lang IS NOT NULL)";
        $data = $this->service->superFunction($apiParams, $sqlParams, $query);

        $data = array_column($data, 'lang');

        // var_export(json_encode($data));

        $pagesLangs = $data;

        return $data;
    }

    public function getPagesUsersLangs(): array
    {

        static $pagesUsersLangs = [];

        if (!empty($pagesUsersLangs ?? [])) {
            return $pagesUsersLangs;
        }

        $sqlParams = [];
        $apiParams = ['get' => 'pages_users', 'distinct' => "1", 'select' => 'lang'];
        $query = "SELECT DISTINCT lang FROM pages_users";
        $data = $this->service->superFunction($apiParams, $sqlParams, $query);

        $data = array_column($data, 'lang');

        $pagesUsersLangs = $data;

        return $data;
    }

    public function getTotalTranslationsCount(?string $lang, string $cand): int
    {

        $table = in_array($cand, ['pages', 'pages_users'], true) ? $cand : 'pages';

        $sqlParams = [];
        $apiParams = ['get' => $table, 'select' => 'count(*)'];

        $query = "select COUNT(*) AS count from $table where target != ''";

        if ($this->service->isValid($lang)) {
            $query .= " AND lang = ?";
            $sqlParams[] = $lang;
            $apiParams['lang'] = $lang;
        }

        $data = $this->service->superFunction($apiParams, $sqlParams, $query);

        $result = (int)($data[0]['count'] ?? 0);

        return $result;
    }

    public function getPagesUsersToMain(?string $lang): array
    {
        static $cache = [];

        if (!empty($cache[$lang] ?? [])) {
            return $cache[$lang];
        }

        $query = "SELECT * FROM pages_users_to_main pum, pages_users pu where pum.id = pu.id";

        $sqlParams = [];
        $apiParams = array('get' => "pages_users_to_main");

        if ($this->service->isValid($lang)) {
            $query .= " AND pu.lang = ?";
            $sqlParams[] = $lang;
            $apiParams['lang'] = $lang;
        }

        $data = $this->service->superFunction($apiParams, $sqlParams, $query);

        $cache[$lang] = $data;

        return $data;
    }
}
