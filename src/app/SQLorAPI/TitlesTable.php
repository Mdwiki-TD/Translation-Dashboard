<?php

namespace App\SQLorAPI;

use App\SQLorAPI\Get\ApiOrSqlService;

class TitlesTable
{
    private static array $titlesInfosCache = [];
    private static array $projectsCache = [];
    private static array $qidsCache = [];
    private static array $translateTypeCache = [];
    private static array $langsCache = [];

    public static function resetCache(): void
    {
        self::$titlesInfosCache = [];
        self::$projectsCache = [];
        self::$qidsCache = [];
        self::$translateTypeCache = [];
        self::$langsCache = [];
    }

    public static function getTitlesInfos(): array
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

        self::$titlesInfosCache = ApiOrSqlService::superFunction($apiParams, [], $qua);

        return self::$titlesInfosCache;
    }

    public static function getProjects(): array
    {
        if (!empty(self::$projectsCache)) {
            return self::$projectsCache;
        }

        $apiParams = ['get' => 'projects'];
        $query = "select g_id, g_title from projects";

        self::$projectsCache = ApiOrSqlService::superFunction($apiParams, [], $query);

        return self::$projectsCache;
    }

    public static function getQids(): array
    {
        if (!empty(self::$qidsCache)) {
            return self::$qidsCache;
        }

        $apiParams = ['get' => 'qids'];
        $query = "SELECT title, qid FROM qids";
        $data = ApiOrSqlService::superFunction($apiParams, [], $query);

        self::$qidsCache = array_column($data, 'qid', 'title');

        return self::$qidsCache;
    }

    public static function getTranslateType(): array
    {
        if (!empty(self::$translateTypeCache)) {
            return self::$translateTypeCache;
        }

        $apiParams = ['get' => 'translate_type'];
        $query = "SELECT tt_title, tt_lead, tt_full FROM translate_type";

        self::$translateTypeCache = ApiOrSqlService::superFunction($apiParams, [], $query);

        return self::$translateTypeCache;
    }

    public static function getLangs(): array
    {
        if (!empty(self::$langsCache)) {
            return self::$langsCache;
        }

        $apiParams = ['get' => 'langs'];
        $query = "SELECT code, autonym, name, redirects FROM langs";

        $data = ApiOrSqlService::superFunction($apiParams, [], $query);
        self::$langsCache = array_column($data, null, 'code');

        return self::$langsCache;
    }

    public static function getQidsForList(array $list): array
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
    return TitlesTable::getTitlesInfos();
}

function getProjects()
{
    return TitlesTable::getProjects();
}

function getQids()
{
    return TitlesTable::getQids();
}


function getTranslateType(): array
{
    return TitlesTable::getTranslateType();
}

function getLangs()
{
    return TitlesTable::getLangs();
}

function getQidsForList($list)
{
    return TitlesTable::getQidsForList($list);
}
