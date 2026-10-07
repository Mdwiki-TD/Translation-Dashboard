<?php

namespace App\SQLorAPI;

use App\SQLorAPI\BaseTable;

class LeaderboardTable extends BaseTable
{

    private static ?self $instance = null;
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }
    public function makeSqlQuery(int|string $year, ?string $userGroup, ?string $cat): array
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

        if ($this->service->isValid($userGroup)) {
            $query .= " AND u.user_group = ?";
            $params[] = $userGroup;
        }

        if ($this->service->isValid($year)) {
            $query .= " AND YEAR(p.pupdate) = ? ";
            $params[] = $year;
        }

        if ($this->service->isValid($cat)) {
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

    public function makeApiParams(int|string $year, ?string $userGroup, ?string $cat): array
    {
        $apiParams = ['get' => 'leaderboard_table'];

        if ($this->service->isValid($year)) {
            $apiParams['year'] = $year;
        }

        if ($this->service->isValid($userGroup)) {
            $apiParams['user_group'] = $userGroup;
        }

        if ($this->service->isValid($cat)) {
            $apiParams['cat'] = $cat;
        }

        return $apiParams;
    }

    public function getLeaderboardTable(int|string $year, ?string $userGroup, ?string $cat): array
    {
        $apiParams = self::makeApiParams($year, $userGroup, $cat);
        $quaData = self::makeSqlQuery($year, $userGroup, $cat);

        return $this->service->superFunction($apiParams, $quaData['params'], $quaData['query']);
    }

    public function getTopLangOfUsers(array $usersOriginal): array
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

        $data = $this->service->superFunction($apiParams, $queryParams, $query);

        // [{"user":"Subas Chandra Rout","lang":"or","cnt":1906},{"user":"Pranayraj1985","lang":"te","cnt":401} ...
        // var_export(json_encode($data));

        if ($users !== $usersOriginal) {
            $data = array_filter($data, function ($item) use ($usersOriginal) {
                return in_array($item['user'], $usersOriginal);
            });
        }

        return $data;
    }

    public function addTopParams(string $query, array $params, array $toAdd): array
    {
        $topParams = [
            "year" => "YEAR(p.pupdate)",
            "month" => "MONTH(p.pupdate)",
            "user_group" => "u.user_group",
            "cat" => "p.cat"
        ];

        foreach ($topParams as $key => $column) {
            if ($this->service->isValid($toAdd[$key] ?? '')) {
                $query .= " AND $column = ?";
                $params[] = $toAdd[$key];
            }
        }

        return [$query, $params];
    }

    public function topQuery(string $select): string
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

    public function getTopUsers(int|string $year, ?string $userGroup, ?string $cat, int|string|null $month = null): array
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

        $data = $this->service->superFunction($apiParams, $params, $query);

        $newData = [];
        foreach ($data as $item) {
            $item["count"] = intval($item["targets"]);
            $newData[$item['user']] = $item;
        }

        return $newData;
    }

    public function getTopLangs(int|string $year, ?string $userGroup, ?string $cat, int|string|null $month = null): array
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

        $data = $this->service->superFunction($apiParams, $params, $query);

        $newData = [];
        foreach ($data as $item) {
            $item["count"] = intval($item["targets"]);
            $newData[$item['lang']] = $item;
        }

        return $newData;
    }

    public function getStatus(int|string $year, ?string $userGroup, ?string $cat): array
    {
        $toAdd = ["year" => $year, "user_group" => $userGroup, "cat" => $cat];
        $apiParams = ['get' => 'status', 'year' => $year, 'user_group' => $userGroup, 'cat' => $cat];

        $query = <<<SQL
            SELECT LEFT(p.pupdate, 7) as date, COUNT(*) as count
            FROM pages p
            LEFT JOIN users u ON p.user = u.username
            WHERE p.target != ''

        SQL;

        [$query, $params] = self::addTopParams($query, [], $toAdd);
        $query .= " GROUP BY 1 ORDER BY 1 ASC";

        $data = $this->service->superFunction($apiParams, $params, $query);

        $newData = [];
        foreach ($data as $item) {
            $newData[$item['date']] = intval($item["count"]);
        }

        return $newData;
    }
    public function getGraphData(
        ?string $lang = null,
        ?string $user = null,
        ?string $year = null,
        ?string $month = null,
        ?string $category = null,
        ?string $campaign = null,
        ?string $user_group = null,
    ): array {
        // api supported_params: [ "lang", "user", "year", "month", "category", "campaign" ]
        $apiParams = [
            'get' => 'graph_data',
            'lang' => $lang,
            'user' => $user,
            'year' => $year,
            'month' => $month,
            'category' => $category,
            'campaign' => $campaign,
            'user_group' => $user_group,
        ];
        $sqlParams = [];

        $query = "SELECT DISTINCT LEFT(p.pupdate, 7) AS date, COUNT(*) AS count
            FROM pages p
            LEFT JOIN categories ca ON p.cat = ca.category
            LEFT JOIN users u ON p.user = u.username
            WHERE p.target != ''
        ";

        if ($this->service->isValid($lang)) {
            $query .= " AND p.lang = ? ";
            $sqlParams[] = $lang;
        }
        if ($this->service->isValid($user)) {
            $query .= " AND p.user = ? ";
            $sqlParams[] = $user;
        }
        if ($this->service->isValid($year)) {
            $query .= " YEAR(p.pupdate) = ? ";
            $sqlParams[] = $year;
        }
        if ($this->service->isValid($month)) {
            $query .= " MONTH(p.pupdate) = ? ";
            $sqlParams[] = $month;
        }

        // applyCampaignCategory
        if ($this->service->isValid($category)) {
            $query .= " p.cat = ? ";
            $sqlParams[] = $category;
        } elseif ($this->service->isValid($campaign)) {
            $query .= " ca.campaign = ? ";
            $sqlParams[] = $campaign;
        }
        if ($this->service->isValid($user_group)) {
            $query .= " u.user_group = ? ";
            $sqlParams[] = $user_group;
        }

        $query .= <<<SQL
            GROUP BY LEFT(p.pupdate, 7)
            ORDER BY LEFT(p.pupdate, 7) ASC
        SQL;

        $uData = $this->service->superFunction($apiParams, $sqlParams, $query);

        $result = [
            'labels' => array_column($uData, 'date'),
            'counts' => array_column($uData, 'count'),
        ];
        return $result;
    }
    public function getGraphDataMainLeaderboard(
        ?string $year = null,
        ?string $month = null,
        ?string $category = null,
        ?string $campaign = null,
        ?string $user_group = null,
    ): array {
        return $this->getGraphData(
            null,
            null,
            $year,
            $month,
            $category,
            $campaign,
            $user_group
        );
    }
}
