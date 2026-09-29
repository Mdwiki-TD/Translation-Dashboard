<?php

namespace App\SQLorAPI\GetDataTab;

use function App\SQLorAPI\Get\superFunction;
use function App\SQLorAPI\Get\isvalid;

function getTitlesInfos()
{

    static $titlesinfos = [];

    if (!empty($titlesinfos ?? [])) {
        return $titlesinfos;
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

    $data = superFunction($apiParams, [], $qua);

    $titlesinfos = $data;

    return $titlesinfos;
}

function getViews($year, $lang)
{

    static $cache = [];

    if (!empty($cache[$year . $lang] ?? [])) {
        return $cache[$year . $lang];
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

    if (isvalid($lang)) {
        $apiParams['lang'] = $lang;
        $sqlParams[] = $lang;

        $queryComplate[] = " v.lang = ? ";
    }

    if (isvalid($year)) {
        $apiParams['year'] = $year;
        $sqlParams[] = $year;

        $queryComplate[] = " YEAR(p.pupdate) = ? ";
    }

    if (!empty($queryComplate)) {
        $query2 .= " WHERE " . implode(" AND ", $queryComplate);
    }

    $data = superFunction($apiParams, $sqlParams, $query2);

    $cache[$year . $lang] = $data;

    return $data;
}

function getSettings()
{

    static $sqlSettings = [];

    if (!empty($sqlSettings)) {
        return $sqlSettings;
    }

    $query = "select id, title, displayed, value, Type from settings";

    $apiParams = ['get' => 'settings'];

    $sqlSettings = superFunction($apiParams, [], $query);

    return $sqlSettings;
}

function getProjects()
{

    static $userGroups = [];

    if (!empty($userGroups ?? [])) {
        return $userGroups;
    }

    $apiParams = ['get' => 'projects'];
    $query = "select g_id, g_title from projects";

    $userGroups = superFunction($apiParams, [], $query);

    return $userGroups;
}

function getCategories()
{

    static $categories = [];

    if (!empty($categories ?? [])) {
        return $categories;
    }

    $apiParams = ['get' => 'categories'];
    $query = "select id, category, category2, campaign, depth, is_default from categories";

    $categories = superFunction($apiParams, [], $query);

    return $categories;
}

function getCategoriesMembers($category)
{

    $apiParams = ['get' => 'category_members', 'cat' => $category];
    $query = "SELECT article_id FROM category_members where category = ?";

    $data = superFunction($apiParams, [$category], $query);

    $result = array_column($data, 'article_id');

    return $result;
}
function getQids()
{

    static $sqlTdQids = [];

    if (!empty($sqlTdQids)) return $sqlTdQids;

    $apiParams = ['get' => 'qids'];
    $query = "SELECT title, qid FROM qids";
    $data = superFunction($apiParams, [], $query);

    $sqlTdQids = array_column($data, 'qid', 'title');

    return $sqlTdQids;
}

function getUsersNoInprocess()
{

    static $users = [];

    if (!empty($users)) return $users;

    $apiParams = ['get' => 'users_no_inprocess'];
    $query = "SELECT id, user, is_active FROM users_no_inprocess order by id";
    $users = superFunction($apiParams, [], $query);

    return $users;
}

function getFullTranslators($column = null)
{

    static $fullTr = [];

    if (!empty($fullTr)) return $fullTr;

    $apiParams = ['get' => 'full_translators'];
    $query = "SELECT id, user, is_active FROM full_translators";
    $fullTr = superFunction($apiParams, [], $query);

    if ($column) {
        return array_column($fullTr, $column);
    }

    return $fullTr;
}

function getTranslateType(): array
{

    static $translateType = [];

    if (!empty($translateType ?? [])) {
        return $translateType;
    }

    $apiParams = ['get' => 'translate_type'];
    $query = "SELECT tt_title, tt_lead, tt_full FROM translate_type";

    $data = superFunction($apiParams, [], $query);

    $translateType = $data;

    return $data;
}

function getCountPages()
{

    static $countPages = [];

    if (!empty($countPages ?? [])) {
        return $countPages;
    }

    $apiParams = ['get' => 'count_pages'];
    $query = <<<SQL
        SELECT DISTINCT user, count(target) as count from pages group by user order by count desc
    SQL;

    $data = superFunction($apiParams, [], $query);

    $data = array_column($data, 'count', 'user');

    arsort($data);

    // print_r($data);

    $countPages = $data;

    return $data;
}

function getLangs()
{

    static $langs = [];

    if (!empty($langs ?? [])) return $langs;

    $apiParams = ['get' => 'langs'];
    $query = "SELECT code, autonym, name, redirects FROM langs";

    $data = superFunction($apiParams, [], $query);

    $langs = array_column($data, null, 'code');

    return $langs;
}

function getQidsForList($list)
{

    $sqQids = getQids();

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
function getCampsToCat()
{
    static $sCampToCat = [];
    if (!empty($sCampToCat)) return $sCampToCat;

    $categoriesTab = getCategories();
    $sCampToCat = array_column($categoriesTab, "category", 'campaign');

    return $sCampToCat;
}
