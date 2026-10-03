<?php

namespace App\SQLorAPI;

use App\SQLorAPI\BaseTable;

class QidsTable extends BaseTable
{
    private static array $qidsCache = [];

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
        self::$qidsCache = [];
    }

    public function getTitlesQids(): array
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
        $sqQids = self::getTitlesQids();

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

    public function getQidsOthers(string $dis): array
    {

        static $cache = [];

        if (!empty($cache[$dis] ?? [])) {
            return $cache[$dis];
        }

        $data = [];

        $sqlParams = [];
        $apiParams = ['get' => 'qids_others', 'dis' => $dis];
        $quaries = [
            'empty' => "select id, title, qid from qids_others where (qid = '' OR qid IS NULL);",
            'all' => "select id, title, qid from qids_others;",
            'duplicate' => <<<SQL
                SELECT
                A.id AS id, A.title AS title, A.qid AS qid,
                B.id AS id2, B.title AS title2, B.qid AS qid2
            FROM
                qids_others A
            JOIN
                qids_others B ON A.qid = B.qid
            WHERE
                A.qid != '' AND A.title != B.title AND A.id != B.id;
            SQL
        ];

        $query = (array_key_exists($dis, $quaries)) ? $quaries[$dis] : $quaries['all'];

        $data = $this->service->superFunction($apiParams, $sqlParams, $query);

        $cache[$dis] = $data;

        return $data;
    }

    public function getQids(string $dis): array
    {

        static $cache = [];

        if (!empty($cache[$dis] ?? [])) {
            return $cache[$dis];
        }

        $data = [];

        $sqlParams = [];
        $apiParams = ['get' => 'qids', 'dis' => $dis];
        $quaries = [
            'empty' => "select id, title, qid from qids where (qid = '' OR qid IS NULL);",
            'all' => "select id, title, qid from qids;",
            'duplicate' => <<<SQL
                SELECT
                A.id AS id, A.title AS title, A.qid AS qid,
                B.id AS id2, B.title AS title2, B.qid AS qid2
            FROM
                qids A
            JOIN
                qids B ON A.qid = B.qid
            WHERE
                A.qid != '' AND A.title != B.title AND A.id != B.id;
            SQL
        ];

        $query = (array_key_exists($dis, $quaries)) ? $quaries[$dis] : $quaries['all'];

        $data = $this->service->superFunction($apiParams, $sqlParams, $query);

        $cache[$dis] = $data;

        return $data;
    }

    /**
     * Fetches a single qid row by column ('qid' or 'title') from 'qids' or 'qids_others'.
     *
     * @param string $column 'qid' or 'title'
     * @param string $value
     * @param string $table 'qids' or 'qids_others'
     * @return array<string, mixed>|null
     */
    public function get_qid_row(string $column, string $value, string $table = 'qids'): ?array
    {
        $allowedColumns = ['qid', 'title'];
        $allowedTables = ['qids', 'qids_others'];

        if (!in_array($column, $allowedColumns, true) || !in_array($table, $allowedTables, true) || empty($value)) {
            return null;
        }

        $result = $this->db->fetchQuery("SELECT * FROM {$table} WHERE {$column} = ?", [$value]);
        return $result[0] ?? null;
    }

    /**
     * Fetches qid string value by title from 'qids' or 'qids_others'.
     *
     * @param string $title
     * @param string $table 'qids' or 'qids_others'
     * @return string|null
     */
    public function get_qid_by_title(string $title, string $table = 'qids'): ?string
    {
        $row = $this->get_qid_row('title', $title, $table);
        return isset($row['qid']) ? (string) $row['qid'] : null;
    }
}
