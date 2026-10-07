<?php

namespace App\Leaderboard\Helpers\Users;

use App\SQLorAPI\PagesTable;
use App\SQLorAPI\InProcessTable;
use App\Leaderboard\Helpers\Filters\LeadHelp;

class UsersSub
{
    private string $langcode;
    private string $username;
    private string|int $year;

    public function __construct(
        string $username,
        int|string $year,
        string $langcode
    ) {
        // Sanitize and format the language code input
        $this->langcode = rawurldecode(str_replace("_", " ", $langcode));

        $this->username = rawurldecode(str_replace("_", " ", $username));
        $this->year = $year;
    }

    private function add_inp(array $pendingItems, array $to_add): array
    {

        foreach ($to_add as $_ => $Taab) {
            $kry = LeadHelp::make_key($Taab);

            if (!in_array($kry, array_keys($pendingItems))) {
                $pendingItems[$kry] = $Taab;
            }
        }

        return $pendingItems;
    }

    private function pagesTables(array $lead_words_table, array $cats_data): array
    {
        $missingItems = [];
        $pendingItems = [];

        $sql_result = (PagesTable::getInstance())->getUserPages($this->username, $this->year, $this->langcode);

        foreach ($sql_result as $yhu => $tabb) {

            // Ensure the campaign is set
            $category = $tabb['cat'] ?? "";
            if (!empty($category) && empty($tabb['campaign'] ?? "")) {
                $tabb["campaign"] = $cats_data[$category] ?? '';
            }

            // Ensure the word count is set
            $word = $tabb['word'] ?? 0;
            if ($word < 1) {
                $tabb['word'] = $lead_words_table[$tabb['title']] ?? 0;
            }

            $kry = LeadHelp::make_key($tabb);

            if (!empty($tabb['target'] ?? '')) {
                $missingItems[$kry] = $tabb;
            } else {
                $pendingItems[$kry] = $tabb;
            }
        }

        return ['missingItems' => $missingItems, 'pendingItems' => $pendingItems];
    }

    public function getTables(array $lead_words_table, array $cats_data): array
    {
        $result = [
            'missingItems' => [],
            'pendingItems' => [],
        ];

        if (empty($this->username)) {
            return $result;
        }

        $p_tables = $this->pagesTables($lead_words_table, $cats_data);

        $missingItems = $p_tables['missingItems'];
        $pendingItems = $p_tables['pendingItems'];

        $to_add = (InProcessTable::getInstance())->getUserProcessNew($this->username, (string)$this->year);

        $pendingItems = $this->add_inp($pendingItems, $to_add);

        krsort($missingItems);
        krsort($pendingItems);

        $result['missingItems'] = $missingItems;
        $result['pendingItems'] = $pendingItems;

        return $result;
    }
}
