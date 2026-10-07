<?php

namespace App\Results27\Tables;

use App\Results27\Rows\InProcessRowBuilder;

/**
 * Renders the table of pages currently being translated.
 */
class InProcessTable extends AbstractResultsTable
{
    private InProcessRowBuilder $rowBuilder;
    private string $langCode;
    private string $cat;
    private string $campaign;
    private bool $inProgressButton;
    private bool $fullTrUser;
    private ?string $globalUsername;
    private array $titlesInfos;
    private bool $userCoord;

    public function __construct(
        string $langCode,
        string $cat,
        string $campaign,
        bool $inProgressButton,
        bool $fullTrUser,
        ?string $globalUsername,
        array $titlesInfos,
        bool $userCoord
    ) {
        $this->rowBuilder       = new InProcessRowBuilder();
        $this->langCode         = $langCode;
        $this->cat              = $cat;
        $this->campaign             = $campaign;
        $this->inProgressButton = $inProgressButton;
        $this->fullTrUser       = $fullTrUser;
        $this->globalUsername   = $globalUsername;
        $this->titlesInfos      = $titlesInfos;
        $this->userCoord        = $userCoord;
    }

    public function render(array $items): string
    {
        // $items = normalizeItems($items);
        // $html = $this->startTable(true, $this->inProgressButton);
        $html = $this->startTable(true);
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
                $this->campaign,
                $inProcessData,
                $this->inProgressButton,
                $isFull,
                $this->fullTrUser,
                $this->globalUsername,
                $titleData,
                $this->userCoord
            );

            $counter++;
        }

        $html .= $this->endTable();
        return $html;
    }
}
