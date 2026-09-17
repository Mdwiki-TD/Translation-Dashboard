<?php

namespace Results\GetResults2026\Tables;

use function Results\ResultsTableHtml\make_table_start;
use function Results\GetResults2026\Rows\make_one_row_new_inprocess;

use Results\GetResults2026\Rows\InProcessRowBuilder;

/**
 * Renders the table of pages currently being translated.
 *
 */
function make_results_table_inprocess(
    $items,
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

    // $items = normalizeItems($items);

    $frist = make_table_start(true, $inProgressButton);

    $rowBuilder       = new InProcessRowBuilder();
    $html = "";
    $counter = 1;

    foreach ($items as $title => $inProcessData) {
        if (empty($title)) {
            continue;
        }

        $title = str_replace("_", " ", $title);

        $titleData = $titlesInfos[$title] ?? [];

        // { "title": "Andes virus infection", "user": "Mr. Ibrahem", "lang": "ar", "cat": "RTT", "translate_type": "all", "word": 0, "add_date": "2026-05-21 00:00:00", "campaign": "Main", "autonym": "العربية" }
        $traType = $inProcessData["translate_type"] ?? "";

        $isFull  = false;

        if (strtolower(substr($title, 0, 6)) == "video:") {
            $traType = "all";
            $isFull  = true;
        }

        // $html .= make_one_row_new_inprocess(
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

class InProcessTable extends AbstractResultsTable
{
    private InProcessRowBuilder $rowBuilder;
    private string $langCode;
    private string $cat;
    private string $camp;
    private bool $inProgressButton;
    private bool $fullTrUser;
    private ?string $globalUsername;
    private array $titlesInfos;
    private string $endpoint;
    private bool $userCoord;

    public function __construct(
        string $langCode,
        string $cat,
        string $camp,
        bool $inProgressButton,
        bool $fullTrUser,
        ?string $globalUsername,
        array $titlesInfos,
        string $endpoint,
        bool $userCoord
    ) {
        $this->rowBuilder       = new InProcessRowBuilder();
        $this->langCode         = $langCode;
        $this->cat              = $cat;
        $this->camp             = $camp;
        $this->inProgressButton = $inProgressButton;
        $this->fullTrUser       = $fullTrUser;
        $this->globalUsername   = $globalUsername;
        $this->titlesInfos      = $titlesInfos;
        $this->endpoint         = $endpoint;
        $this->userCoord        = $userCoord;
    }

    public function render(array $items): string
    {
        return make_results_table_inprocess(
            $items,
            $this->langCode,
            $this->cat,
            $this->camp,
            $this->inProgressButton,
            $this->fullTrUser,
            $this->globalUsername,
            $this->titlesInfos,
            $this->endpoint,
            $this->userCoord
        );
    }
}
