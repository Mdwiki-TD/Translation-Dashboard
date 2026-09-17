<?php

namespace Results\GetResults2026\Tables;

use Results\GetResults2026\Rows\InProcessRowBuilder;

/**
 * Renders the table of pages currently being translated.
 */
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
        // $items = normalizeItems($items);
        $html = $this->startTable(true, $this->inProgressButton);
        $counter = 1;

        foreach ($items as $title => $inProcessData) {
            if (empty($title)) {
                continue;
            }

            // { "title": "Andes virus infection", "user": "Mr. Ibrahem", "lang": "ar", "cat": "RTT", "translate_type": "all", "word": 0, "add_date": "2026-05-21 00:00:00", "campaign": "Main", "autonym": "العربية" }
            $titleData = $this->titlesInfos[$title] ?? [];
            $title = str_replace("_", " ", $title);

            $traType = $inProcessData["translate_type"] ?? "lead";
            $isFull  = false;

            if (str_starts_with(strtolower($title), "video:")) {
                $traType = "all";
                $isFull  = true;
            }

            $html .= $this->rowBuilder->build(
                $title,
                $traType,
                $counter,
                $this->langCode,
                $this->cat,
                $this->camp,
                $inProcessData,
                $this->inProgressButton,
                $isFull,
                $this->fullTrUser,
                $this->globalUsername,
                $titleData,
                $this->endpoint,
                $this->userCoord
            );

            $counter++;
        }

        $html .= $this->endTable();
        return $html;
    }
}
