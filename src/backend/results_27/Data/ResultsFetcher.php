<?php

namespace Results\GetResults2026\Data;

use function TD\Render\Html\make_mdwiki_cat_url;
use function SQLorAPI\Funcs\get_lang_pages_by_cat;
use function SQLorAPI\Process\get_lang_in_process;
use function SQLorAPI\Funcs\missing_by_lang_and_category;
use function SQLorAPI\Funcs\exists_by_lang_and_category;
use function TD\Render\TestPrint\test_print;

/**
 * Responsible for fetching and preparing all result data
 * (exists, missing, in-process) for a given category and language.
 */
class ResultsFetcher
{
    private bool $debug;

    public function __construct(bool $debug = false)
    {
        $this->debug = $debug;
    }

    /**
     * Main method: returns structured results.
     *
     * @return array{
     *     ix: string,
     *     inprocess: array,
     *     exists: array,
     *     missing: array
     * }
     */
    public function get(string $cat, string $code): array
    {
        // Pages that already exist via Translation Dashboard
        $existsViaTd = get_lang_pages_by_cat($code, $cat);
        $existsViaTd = array_column($existsViaTd, null, "title");
        $this->log("exists_via_td", count($existsViaTd));

        // Missing pages
        // { "title": "Alpha-gal syndrome", "category": "RTT", "importance": "Mid", "r_lead_refs": 0, "r_all_refs": 0, "en_views": 15, "w_lead_words": 0, "w_all_words": 0, "qid": "Q16242785" }
        $itemsMissing = missing_by_lang_and_category($code, $cat);
        $this->log("Items missing", count($itemsMissing));

        // Existing pages
        // { "title": "11p deletion syndrome", "category": "RTT", "importance": "", "r_lead_refs": 5, "r_all_refs": 14, "en_views": 838, "w_lead_words": 221, "w_all_words": 547, "qid": "Q1892153", "target": "متلازمة واجر" }
        $itemsExists = exists_by_lang_and_category($code, $cat);
        $itemsExists = array_column($itemsExists, null, "title");

        // Mark origin of each existing page
        // add column to all $itemsExists ("via" => "before") or ("via" => "td") if title in $existsViaTd
        foreach ($itemsExists as $title => &$item) {
            $item["via"] = isset($existsViaTd[$title]) ? "td" : "before";
        }
        unset($item);

        $lenExists = count($itemsExists);
        $this->log("Items exists", $lenExists);

        // In-process items that are still in the missing list
        $missingTitles = array_column($itemsMissing, "title");
        $inProcess = $this->getInProcess($missingTitles, $code);

        // Remove in-process titles from the missing list
        $missing = $itemsMissing;

        if (!empty($inProcess)) {
            $inProcessTitles = array_flip(array_column($inProcess, "title"));
            $missing = array_filter($itemsMissing, static function (array $item) use ($inProcessTitles): bool {
                return !isset($inProcessTitles[$item["title"]]);
            });
        }

        $summary = $this->createSummary(
            $code,
            $cat,
            count($inProcess),
            count($missing),
            $lenExists
        );

        // Sort existing pages by title
        ksort($itemsExists);

        return [
            "ix"        => $summary,
            "inprocess" => $inProcess,
            "exists"    => $itemsExists,
            "missing"   => array_values($missing),
        ];
    }

    /**
     * Filter in-process records that belong to the current missing list.
     */
    private function getInProcess(array $missingTitles, string $code): array
    {
        $res = get_lang_in_process($code);
        $result = [];

        foreach ($res as $row) {
            if (in_array($row["title"], $missingTitles, true)) {
                $result[$row["title"]] = $row;
            }
        }

        return $result;
    }

    /**
     * Build the human-readable summary string.
     */
    private function createSummary(
        string $code,
        string $cat,
        int $lenInProcess,
        int $lenMissing,
        int $lenExists
    ): string {
        $total  = $lenExists + $lenMissing + $lenInProcess;
        // Prepare category URL
        $catUrl = make_mdwiki_cat_url($cat, "Category");

        // Generate summary message
        return sprintf(
            "Found %d pages in %s, %d exists, and %d missing in (<a href='https://%s.wikipedia.org' target='_blank'>%s</a>), %d In process.",
            $total,
            $catUrl,
            $lenExists,
            $lenMissing,
            $code,
            $code,
            $lenInProcess
        );
    }

    private function log(string $message, $value = null): void
    {
        if (!$this->debug) {
            return;
        }

        $text = $value !== null ? "{$message}: {$value}" : $message;
        test_print($text);
    }
}
