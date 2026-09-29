<?php

namespace App\SQLorAPI;

use App\SQLorAPI\ApiOrSqlService;

class QidsTable
{
    private static array $qidsCache = [];

    private ApiOrSqlService $service;
    public function __construct(?ApiOrSqlService $service = null)
    {
        $this->service = $service ?? new ApiOrSqlService();
    }

    public static function resetCache(): void
    {
        self::$qidsCache = [];
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

function getQids()
{
    return (new QidsTable())->getQids();
}

function getQidsForList($list)
{
    return (new QidsTable())->getQidsForList($list);
}
