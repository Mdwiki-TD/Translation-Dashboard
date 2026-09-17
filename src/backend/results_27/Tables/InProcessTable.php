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
        $html = $this->startTable(true, $this->inProgressButton);
        $counter = 1;

        foreach ($items as $title => $inProcessData) {
            if (empty($title)) {
                continue;
            }

            $title = str_replace('_', ' ', $title);
            $titleData = $this->titlesInfos[$title] ?? [];

            $traType = $inProcessData['translate_type'] ?? 'lead';
            $isFull  = false;

            if (str_starts_with(strtolower($title), 'video:')) {
                $traType = 'all';
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
