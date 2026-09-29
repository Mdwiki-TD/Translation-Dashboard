<?php

namespace App\SQLorAPI;

use App\SQLorAPI\ApiOrSqlService;

class TitlesTable
{
    private static array $titlesInfosCache = [];
    private static array $projectsCache = [];
    private static array $qidsCache = [];
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
        self::$qidsCache = [];
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

    public function getQids(): array
    {
        if (!empty(self::$qidsCache)) {
            return self::$qidsCache;
        }

        $apiParams = ['get' => 'qids'];
        $query = "SELECT title, qid FROM qids";
        $data = $this->service->superFunction($apiParams, [], $query);

        self::$qidsCache = array_column($data, 'qid', 'title');

        return self::$qidsCache;
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

    public function getQidsForList(array $list): array
    {
        $sqQids = self::getQids();

        $withQids = [];
        $noQids = [];

        foreach ($list as $member) {
            $qid = $sqQids[$member] ?? 0;
            if ($qid) {
                $withQids[$member] = $qid;
            } else {
                $noQids[] = $member;
            }
        }

        return [
            "with_qids" => $withQids,
            "no_qids" => $noQids,
        ];
    }
}


function getTitlesInfos()
{
    return (new TitlesTable())->getTitlesInfos();
}

function getProjects()
{
    return (new TitlesTable())->getProjects();
}

function getQids()
{
    return (new TitlesTable())->getQids();
}


function getTranslateType(): array
{
    return (new TitlesTable())->getTranslateType();
}

function getLangs()
{
    return (new TitlesTable())->getLangs();
}

function getQidsForList($list)
{
    return (new TitlesTable())->getQidsForList($list);
}
