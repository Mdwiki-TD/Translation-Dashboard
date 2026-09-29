<?php

namespace App\SQLorAPI;

use App\SQLorAPI\BaseTable;

class RecentTable extends BaseTable
{
    private static array $PagesWithViewsCache = [];
    private static array $RecentPagesUsersCache = [];


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
        self::$PagesWithViewsCache = [];
        self::$RecentPagesUsersCache = [];
    }

    public function getRecentPagesWithViews(string $lang): array
    {

        if (!empty(self::$PagesWithViewsCache[$lang] ?? [])) {
            return self::$PagesWithViewsCache[$lang];
        }

        $langLine = '';

        $sqlParams = [];

        $apiParams = [
            'get' => 'pages_with_views',
            'target' => 'not_empty',
            "order" => 'pupdate_or_add_date',
            'limit' => '250',
        ];

        if (!empty($lang) && $lang != 'All') {
            $langLine = "and p.lang = ?";
            $sqlParams[] = $lang;

            $apiParams['lang'] = $lang;
        }

        $sqlQuery = <<<SQL
            select distinct
                p.id, p.title, p.word, p.translate_type, p.cat,
                p.lang, p.user, p.target, p.date, p.pupdate,
                p.add_date, p.deleted, p.mdwiki_revid,
                (select v.views from views_new_all v where p.target = v.target AND p.lang = v.lang LIMIT 1) as views
            from pages p
            where p.target != ''
            $langLine
            ORDER BY GREATEST(UNIX_TIMESTAMP(p.pupdate), UNIX_TIMESTAMP(p.add_date)) DESC
            limit 250
        SQL;

        $tab = $this->service->superFunction($apiParams, $sqlParams, $sqlQuery);

        self::$PagesWithViewsCache[$lang] = $tab;

        return $tab;
    }

    public function getRecentPagesUsers(string $lang): array
    {
        if (!empty(self::$RecentPagesUsersCache[$lang] ?? [])) {
            return self::$RecentPagesUsersCache[$lang];
        }

        $sqlParams = [];

        $apiParams = [
            'get' => 'pages_users',
            'target' => 'not_empty',
            "order" => 'pupdate',
            'limit' => '100'
        ];

        $langLine = '';

        if (!empty($lang) && $lang != 'All') {
            $langLine = "and lang = ?";
            $sqlParams[] = $lang;
            $apiParams['lang'] = $lang;
        };

        $qua = <<<SQL
            select * #id, date, user, lang, title, cat, word, target, pupdate, add_date
            from pages_users
            where
                target != ''
            # and title not in ( select p.title from pages p where p.lang = lang and p.target != '' )
            $langLine
            ORDER BY pupdate DESC
            limit 100
        SQL;

        $tab = $this->service->superFunction($apiParams, $sqlParams, $qua);

        // sort the table by add_date
        usort($tab, function ($a, $b) {
            return strtotime($b['pupdate']) - strtotime($a['pupdate']);
        });

        self::$RecentPagesUsersCache[$lang] = $tab;

        return $tab;
    }

    public function getRecentTranslated(string $lang, string $table, int $limit, int $offset): array
    {

        $sqlParams = [];
        $apiParams = array('get' => $table, "order" => 'pupdate', 'limit' => $limit, 'offset' => $offset);

        $query = "SELECT * FROM $table WHERE target != ''";

        if (!empty($lang) && $lang != 'All') {
            $query .= " AND lang = ?";
            $sqlParams[] = $lang;
            $apiParams['lang'] = $lang;
        }

        $query .= " ORDER BY pupdate DESC ";

        // add limit and offset to $sqlLine
        if ($limit > 0) {
            $query .= " \n LIMIT $limit ";
            // $query .= " \n LIMIT ? ";
            // $sqlParams[] = $limit;
        }

        if ($offset > 0) {
            $query .= " OFFSET $offset ";
            // $query .= " OFFSET ? ";
            // $sqlParams[] = $offset;
        }

        $dd = $this->service->superFunction($apiParams, $sqlParams, $query);

        // sort the table by add_date
        usort($dd, function ($a, $b) {
            return strtotime($b['add_date']) - strtotime($a['add_date']);
        });

        return $dd;
    }

}

function getRecentPagesWithViews(string $lang): array
{
    return (RecentTable::getInstance())->getRecentPagesWithViews($lang);
}

function getRecentPagesUsers(string $lang): array
{
    return (RecentTable::getInstance())->getRecentPagesUsers($lang);
}

function getRecentTranslated(string $lang, string $table, int $limit, int $offset): array
{
    return (RecentTable::getInstance())->getRecentTranslated($lang, $table, $limit, $offset);
}

