<?php

namespace Results\GetResults2026\Tables;

use function Results\ResultsTableHtml\make_table_start;
use function Results\GetResults2026\Rows\make_one_row_new_inprocess;

use Results\GetResults2026\Rows\InProcessRowBuilder;

/**
 * Renders the table of pages currently being translated.
 */
function make_results_table_inprocess(
    $inProcessData,
    string $langCode,
        string $cat,
        string $camp,
        bool $inProgressButton,
        bool $fullTrUser,
        ?string $globalUsername,
        array $titlesInfos,
    string $endpoint,
    bool $userCoord
): string {

    // $inProcessData = normalizeItems($inProcessData);

    $frist = make_table_start(true, $inProgressButton);

    $rowBuilder       = new InProcessRowBuilder();
    $html = "";
    $counter = 1;

    foreach ($inProcessData as $title => $inProcessData) {
        if (empty($title)) {
            continue;
        }

        $title = str_replace("_", " ", $title);

        $titleData = $titlesInfos[$title] ?? [];

        // { "title": "Andes virus infection", "user": "Mr. Ibrahem", "lang": "ar", "cat": "RTT", "translate_type": "all", "word": 0, "add_date": "2026-05-21 00:00:00", "campaign": "Main", "autonym": "العربية" }
        $traType = $inProcessData['translate_type'] ?? '';

        $isFull = false;

        if (strtolower(substr($title, 0, 6)) == 'video:') {
            $traType = 'all';
            $isFull = true;
        };

        $html .= $rowBuilder->build(
            $title,
            $traType,
            $counter,
            $langCode,
            $cat,
            $camp,
            $inProcessData,
            $inProgressButton,
            $isFull,
            $fullTrUser,
            $globalUsername,
            $titleData,
            $endpoint,
            $userCoord
        );

        $counter++;
    };

    $last = <<<HTML
        </tbody>
    </table>
    HTML;

    return $frist . $html . $last;
}
