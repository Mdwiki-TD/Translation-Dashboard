<?php

namespace App\SQLorAPI\Categories;

use App\SQLorAPI\Get\ApiOrSqlService;

class CategoriesTable
{
    private static array $categoriesCache = [];
    private static array $campsToCatCache = [];
    private static array $countMembersCache = [];
    private static array $staticsCache = [];

    public static function resetCache(): void
    {
        self::$categoriesCache = [];
        self::$campsToCatCache = [];
        self::$countMembersCache = [];
        self::$staticsCache = [];
    }

    public static function getTdOrSqlCategories(): array
    {
        if (!empty(self::$categoriesCache)) {
            return self::$categoriesCache;
        }

        $apiParams = ['get' => 'categories'];
        $query = "select id, category, category2, campaign, depth, is_default from categories";

        $categories = ApiOrSqlService::superFunction($apiParams, [], $query);
        self::$categoriesCache = $categories;

        return self::$categoriesCache;
    }

    public static function getTdOrSqlCategoriesMembers(mixed $category): array
    {
        $apiParams = ['get' => 'category_members', 'cat' => $category];
        $query = "SELECT article_id FROM category_members where category = ?";

        $data = ApiOrSqlService::superFunction($apiParams, [$category], $query);

        return array_column($data, 'article_id');
    }

    public static function getCampsToCat(): array
    {
        if (!empty(self::$campsToCatCache)) {
            return self::$campsToCatCache;
        }

        $categoriesTab = self::getTdOrSqlCategories();
        self::$campsToCatCache = array_column($categoriesTab, "category", 'campaign');

        return self::$campsToCatCache;
    }

    public static function countCategoryMembers(mixed $category): array
    {
        if ($category === null) {
            $category = "RTT";
        }

        $key = (string)$category;

        if (!empty(self::$countMembersCache[$key] ?? [])) {
            return self::$countMembersCache[$key];
        }

        $query = <<<SQL
            SELECT
                COUNT(c.article_id) AS members
            FROM
                category_members c
            where c.category = ?
        SQL;

        $params = [$category];

        $u_data = ApiOrSqlService::superFunction([], $params, $query);
        self::$countMembersCache[$key] = $u_data;

        return self::$countMembersCache[$key];
    }

    public static function missingByLangAndCategory(mixed $langCode, mixed $category): array
    {
        $apiParams = ['get' => 'missing_by_lang_and_category', 'category' => $category, 'lang' => $langCode];

        $query = <<<SQL
            SELECT
                c.article_id AS title,
                c.category AS category,
                ase.importance,
                rc.r_lead_refs,
                rc.r_all_refs,
                ep.en_views,
                q.qid,
                w.w_lead_words,
                w.w_all_words
            FROM
                category_members c

            JOIN qids q                     ON q.title      = c.article_id
            LEFT JOIN all_qids_exists aq    ON aq.qid       = q.qid AND aq.code = ?

            LEFT JOIN assessments ase       ON ase.title    = c.article_id
            LEFT JOIN enwiki_pageviews ep   ON ep.title     = c.article_id
            LEFT JOIN refs_counts rc        ON rc.r_title   = c.article_id
            LEFT JOIN words w               ON w.w_title    = c.article_id
            WHERE
                c.category = ?
            AND aq.target IS NULL

            /* to work with valid langs */
            AND EXISTS ( SELECT 1 FROM langs la WHERE la.code = ? )
        SQL;

        $params = [$langCode, $category, $langCode];

        return ApiOrSqlService::superFunction($apiParams, $params, $query);
    }

    public static function existsByLangAndCategory(mixed $langCode, mixed $category): array
    {
        $apiParams = ['get' => 'exists_by_lang_and_category', 'category' => $category, 'lang' => $langCode];

        $query = <<<SQL
            SELECT
                c.article_id AS title,
                c.category AS category,
                ase.importance,
                rc.r_lead_refs,
                rc.r_all_refs,
                ep.en_views,
                q.qid,
                w.w_lead_words,
                w.w_all_words,
                aq.target
            FROM
                category_members c

            JOIN qids q                ON q.title = c.article_id
            LEFT JOIN all_qids_exists aq    ON aq.qid = q.qid AND aq.code = ?

            LEFT JOIN assessments ase       ON ase.title    = c.article_id
            LEFT JOIN enwiki_pageviews ep   ON ep.title     = c.article_id
            LEFT JOIN refs_counts rc        ON rc.r_title   = c.article_id
            LEFT JOIN words w               ON w.w_title    = c.article_id
            WHERE
                c.category = ?
            AND aq.target IS NOT NULL

            /* to work with valid langs */
            AND EXISTS ( SELECT 1 FROM langs la WHERE la.code = ? )
        SQL;

        $params = [$langCode, $category, $langCode];

        return ApiOrSqlService::superFunction($apiParams, $params, $query);
    }

    public static function staticsByCategory(mixed $category): array
    {
        if ($category === null) {
            $category = "RTT";
        }

        $key = (string)$category;

        if (!empty(self::$staticsCache[$key] ?? [])) {
            return self::$staticsCache[$key];
        }

        $apiParams = ['get' => 'statics_by_category', 'category' => $category];

        $query = <<<SQL
            SELECT
                aq.code AS language_code,
                COUNT(*) AS available_title_count
            FROM
                category_members c
                JOIN qids q ON q.title = c.article_id
                JOIN all_qids_exists aq ON aq.qid = q.qid
            WHERE
                c.category = ?
            GROUP BY
                aq.code
            ORDER BY
                available_title_count ASC;
        SQL;

        $params = [$category];

        $u_data = ApiOrSqlService::superFunction($apiParams, $params, $query);
        self::$staticsCache[$key] = $u_data;

        return self::$staticsCache[$key];
    }
}
