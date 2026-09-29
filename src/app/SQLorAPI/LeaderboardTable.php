<?php

namespace App\SQLorAPI\Leaderboard;

use App\SQLorAPI\Get\ApiOrSqlService;

class LeaderboardTable
{
    public static function makeSqlQuery(mixed $year, mixed $userGroup, mixed $cat): array
    {
        $params = [];

        $query = "SELECT p.title,
            p.target, p.cat, p.lang, p.word, YEAR(p.pupdate) AS pup_y, p.user, u.user_group, LEFT(p.pupdate, 7) as m, v.views
            FROM pages p
            LEFT JOIN users u
                ON p.user = u.username
            LEFT JOIN views_new_all v
                ON p.target = v.target
                AND p.lang = v.lang
            WHERE p.target != ''
        ";

        if (ApiOrSqlService::isValid($userGroup)) {
            $query .= " AND u.user_group = ?";
            $params[] = $userGroup;
        }

        if (ApiOrSqlService::isValid($year)) {
            $query .= " AND YEAR(p.pupdate) = ? ";
            $params[] = $year;
        }

        if (ApiOrSqlService::isValid($cat)) {
            $query .= " AND p.cat = ? ";
            $params[] = $cat;
        }

        $query .= " \n group by v.target, v.lang \n";
        $query .= " ORDER BY 1 DESC";

        return [
            'query' => $query,
            'params' => $params
        ];
    }

    public static function makeApiParams(mixed $year, mixed $userGroup, mixed $cat): array
    {
        $apiParams = ['get' => 'leaderboard_table'];

        if (ApiOrSqlService::isValid($year)) {
            $apiParams['year'] = $year;
        }

        if (ApiOrSqlService::isValid($userGroup)) {
            $apiParams['user_group'] = $userGroup;
        }

        if (ApiOrSqlService::isValid($cat)) {
            $apiParams['cat'] = $cat;
        }

        return $apiParams;
    }

    public static function getLeaderboardTable(mixed $year, mixed $userGroup, mixed $cat): array
    {
        $apiParams = self::makeApiParams($year, $userGroup, $cat);
        $quaData = self::makeSqlQuery($year, $userGroup, $cat);

        return ApiOrSqlService::superFunction($apiParams, $quaData['params'], $quaData['query']);
    }

    public static function getTopLangOfUsers(array $usersOriginal): array
    {
        $users = (count($usersOriginal) > 50) ? [] : $usersOriginal;

        $apiParams = ['get' => 'top_lang_of_users', 'users' => $users];

        $queryParams = [];
        $queryLine = "";

        if (!empty($users)) {
            $placeholders = rtrim(str_repeat('?,', count($users)), ',');
            $queryLine = " AND p.user IN ($placeholders)";
            $queryParams = $users;
        }

        $query = <<<SQL
            SELECT user, lang, cnt
            FROM (
                SELECT p.user, p.lang, COUNT(p.target) AS cnt,
                    ROW_NUMBER() OVER (PARTITION BY p.user ORDER BY COUNT(p.target) DESC) AS rn
                FROM pages p
                WHERE p.target != ''
                AND p.target IS NOT NULL
                $queryLine
                GROUP BY p.user, p.lang
            ) AS ranked
            WHERE rn = 1
            ORDER BY cnt DESC;
        SQL;

        $data = ApiOrSqlService::superFunction($apiParams, $queryParams, $query);

        if ($users !== $usersOriginal) {
            $data = array_filter($data, function ($item) use ($usersOriginal) {
                return in_array($item['user'], $usersOriginal);
            });
        }

        return $data;
    }

    public static function addTopParams(string $query, array $params, array $toAdd): array
    {
        $topParams = [
            "year" => "YEAR(p.pupdate)",
            "month" => "MONTH(p.pupdate)",
            "user_group" => "u.user_group",
            "cat" => "p.cat"
        ];

        foreach ($topParams as $key => $column) {
            if (ApiOrSqlService::isValid($toAdd[$key] ?? '')) {
                $query .= " AND $column = ?";
                $params[] = $toAdd[$key];
            }
        }

        return [$query, $params];
    }

    public static function topQuery(string $select): string
    {
        $selectField = ($select === 'user') ? 'p.user' : 'p.lang';

        return <<<SQL
            SELECT
                $selectField,
                COUNT(p.target) AS targets,
                SUM(CASE
                    WHEN p.word IS NOT NULL AND p.word != 0 AND p.word != '' THEN p.word
                    WHEN translate_type = 'all' THEN w.w_all_words
                    ELSE w.w_lead_words
                END) AS words,
                SUM(
                    CASE
                        WHEN v.views IS NULL OR v.views = '' THEN 0
                        ELSE CAST(v.views AS UNSIGNED)
                    END
                    ) AS views

            FROM pages p

            LEFT JOIN users u
                ON p.user = u.username

            LEFT JOIN words w
                ON w.w_title = p.title

            LEFT JOIN views_new_all v
                ON p.target = v.target AND p.lang = v.lang

            WHERE p.target != '' AND p.target IS NOT NULL
            AND p.user != '' AND p.user IS NOT NULL
            AND p.lang != '' AND p.lang IS NOT NULL
            SQL;
    }

    public static function getTopUsers(mixed $year, mixed $userGroup, mixed $cat, mixed $month = null): array
    {
        $toAdd = [
            "year" => $year,
            "user_group" => $userGroup,
            "cat" => $cat,
            "month" => $month,
        ];

        $apiParams = [
            'get' => 'top_users',
            'year' => $year,
            'user_group' => $userGroup,
            'cat' => $cat,
            'month' => $month,
        ];

        $query = self::topQuery('user');
        [$query, $params] = self::addTopParams($query, [], $toAdd);
        $query .= " GROUP BY p.user ORDER BY 2 DESC";

        $data = ApiOrSqlService::superFunction($apiParams, $params, $query);

        $newData = [];
        foreach ($data as $item) {
            $item["count"] = intval($item["targets"]);
            $newData[$item['user']] = $item;
        }

        return $newData;
    }

    public static function getTopLangs(mixed $year, mixed $userGroup, mixed $cat, mixed $month = null): array
    {
        $toAdd = [
            "year" => $year,
            "user_group" => $userGroup,
            "cat" => $cat,
            'month' => $month,
        ];

        $apiParams = [
            'get' => 'top_langs',
            'year' => $year,
            'user_group' => $userGroup,
            'cat' => $cat,
            'month' => $month,
        ];

        $query = self::topQuery('lang');
        [$query, $params] = self::addTopParams($query, [], $toAdd);
        $query .= " GROUP BY p.lang ORDER BY 2 DESC";

        $data = ApiOrSqlService::superFunction($apiParams, $params, $query);

        $newData = [];
        foreach ($data as $item) {
            $item["count"] = intval($item["targets"]);
            $newData[$item['lang']] = $item;
        }

        return $newData;
    }

    public static function getStatus(mixed $year, mixed $userGroup, mixed $cat): array
    {
        $toAdd = ["year" => $year, "user_group" => $userGroup, "cat" => $cat];
        $apiParams = ['get' => 'status', 'year' => $year, 'user_group' => $userGroup, 'cat' => $cat];

        $query = <<<SQL
            SELECT LEFT(p.pupdate, 7) as date, COUNT(*) as count

            FROM pages p

            LEFT JOIN users u
                ON p.user = u.username

            WHERE p.target != ''

        SQL;

        [$query, $params] = self::addTopParams($query, [], $toAdd);
        $query .= " GROUP BY 1 ORDER BY 1 ASC";

        $data = ApiOrSqlService::superFunction($apiParams, $params, $query);

        $newData = [];
        foreach ($data as $item) {
            $newData[$item['date']] = intval($item["count"]);
        }

        return $newData;
    }
}
