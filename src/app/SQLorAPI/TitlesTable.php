<?php

namespace App\SQLorAPI;

use App\SQLorAPI\ApiOrSqlService;

class TitlesTable
{
    private static array $titlesInfosCache = [];
    private static array $projectsCache = [];
    private static array $translateTypeCache = [];
    private static array $langsCache = [];

    private ApiOrSqlService $service;
    public function __construct(?ApiOrSqlService $service = null)
    {
        $this->service = $service ?? new ApiOrSqlService();
    }

    public static function resetCache(): void
    {
        self::$titlesInfosCache = [];
        self::$projectsCache = [];
        self::$translateTypeCache = [];
        self::$langsCache = [];
    }

    public function getTitlesInfos(): array
    {
        if (!empty(self::$titlesInfosCache)) {
            return self::$titlesInfosCache;
        }

        $apiParams = ['get' => 'titles'];

        $qua = <<<SQL
            select
                ase.title AS title,
                ase.importance AS importance,
                rc.r_lead_refs AS r_lead_refs,
                rc.r_all_refs AS r_all_refs,
                ep.en_views AS en_views,
                w.w_lead_words AS w_lead_words,
                w.w_all_words AS w_all_words,
                q.qid AS qid
            from
                assessments ase
                left join enwiki_pageviews ep   on ep.title   = ase.title
                left join qids q                on q.title    = ase.title
                left join refs_counts rc        on rc.r_title = ase.title
                left join words w               on w.w_title  = ase.title
        SQL;

        self::$titlesInfosCache = $this->service->superFunction($apiParams, [], $qua);

        return self::$titlesInfosCache;
    }
    public function getTitlesInfosByTitles(?array $titles): array
    {
        // Ensure $titles is an array
        if (!is_array($titles)) {
            $titles = [];
        }

        $apiParams = ['get' => 'titles', 'titles' => $titles];

        $qua = <<<SQL
            select
                ase.title AS title,
                ase.importance AS importance,
                rc.r_lead_refs AS r_lead_refs,
                rc.r_all_refs AS r_all_refs,
                ep.en_views AS en_views,
                w.w_lead_words AS w_lead_words,
                w.w_all_words AS w_all_words,
                q.qid AS qid
            from
                assessments ase
                left join enwiki_pageviews ep   on ep.title   = ase.title
                left join qids q                on q.title    = ase.title
                left join refs_counts rc        on rc.r_title = ase.title
                left join words w               on w.w_title  = ase.title
        SQL;

        $sqlParams = [];

        if (!empty($titles)) {
            $placeholders = rtrim(str_repeat('?,', count($titles)), ',');
            $qua .= " WHERE ase.title IN ($placeholders)";
            $sqlParams = $titles;              // pass to superFunction
        }

        $data = $this->service->superFunction($apiParams, $sqlParams, $qua);

        return $data;
    }

    public function getProjects(): array
    {
        if (!empty(self::$projectsCache)) {
            return self::$projectsCache;
        }

        $apiParams = ['get' => 'projects'];
        $query = "select g_id, g_title from projects";

        self::$projectsCache = $this->service->superFunction($apiParams, [], $query);

        return self::$projectsCache;
    }

    public function getTranslateType(): array
    {
        if (!empty(self::$translateTypeCache)) {
            return self::$translateTypeCache;
        }

        $apiParams = ['get' => 'translate_type'];
        $query = "SELECT tt_title, tt_lead, tt_full FROM translate_type";

        self::$translateTypeCache = $this->service->superFunction($apiParams, [], $query);

        return self::$translateTypeCache;
    }

    public function getLangs(): array
    {
        if (!empty(self::$langsCache)) {
            return self::$langsCache;
        }

        $apiParams = ['get' => 'langs'];
        $query = "SELECT code, autonym, name, redirects FROM langs";

        $data = $this->service->superFunction($apiParams, [], $query);
        self::$langsCache = array_column($data, null, 'code');

        return self::$langsCache;
    }

    public function get_publish_reports_stats(): array
    {

        static $statsData = [];

        if (!empty($statsData)) {
            return $statsData;
        }

        $query = <<<SQL
            SELECT DISTINCT YEAR(date) as year, MONTH(date) as month, lang, user, result
            FROM publish_reports
            GROUP BY year, month, lang, user, result
        SQL;

        $apiParams = ['get' => 'publish_reports_stats'];

        $statsData = $this->service->superFunction($apiParams, [], $query);

        return $statsData;
    }

}
