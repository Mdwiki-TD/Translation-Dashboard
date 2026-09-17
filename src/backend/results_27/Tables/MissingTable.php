<?php

namespace Results\GetResults2026\Tables;

use Results\GetResults2026\Rows\MissingRowBuilder;

/**
 * Renders the table of missing pages.
 */
class MissingTable extends AbstractResultsTable
{
    private MissingRowBuilder $rowBuilder;
    private string $langCode;
    private string $cat;
    private string $camp;
    private string $traType;
    private bool $fullTrUser;
    private ?string $globalUsername;
    private array $noLeadTranslates;
    private array $fullTranslates;

    public function __construct(
        string $langCode,
        string $cat,
        string $camp,
        string $traType,
        bool $fullTrUser,
        ?string $globalUsername,
        array $noLeadTranslates,
        array $fullTranslates
    ) {
        $this->rowBuilder       = new MissingRowBuilder();
        $this->langCode         = $langCode;
        $this->cat              = $cat;
        $this->camp             = $camp;
        $this->traType          = $traType;
        $this->fullTrUser       = $fullTrUser;
        $this->globalUsername   = $globalUsername;
        $this->noLeadTranslates = $noLeadTranslates;
        $this->fullTranslates   = $fullTranslates;
    }

    public function render(array $items): string
    {
        $doFull = ($this->traType !== "all");

        // Sort by English page views (descending)
        usort($items, static function (array $a, array $b): int {
            return ($b["en_views"] ?? 0) <=> ($a["en_views"] ?? 0);
        });

        $items = array_column($items, null, "title");

        $html = $this->startTable(false, false);
        $counter = 1;

        foreach ($items as $title => $titleData) {
            if (empty($title)) {
                continue;
            }

            $title = str_replace("_", " ", $title);

            $row = $this->rowBuilder->build(
                $title,
                $this->traType,
                $counter,
                $this->langCode,
                $this->cat,
                $this->camp,
                false,
                $this->fullTrUser,
                $this->globalUsername,
                $titleData
            );

            // Special handling when full translation is restricted
            if (!$doFull || $this->fullTrUser) {
                $html .= $row;
                $counter++;
                continue;
            }

            $noLead = in_array($title, $this->noLeadTranslates, true);
            $isFull   = in_array($title, $this->fullTranslates, true);

            if ($noLead && !$isFull) {
                continue;
            }

            if (!$noLead) {
                $html .= $row;
            }

            if ($isFull) {
                $html .= $this->rowBuilder->build(
                    $title,
                    "all",
                    $counter,
                    $this->langCode,
                    $this->cat,
                    $this->camp,
                    true,
                    $this->fullTrUser,
                    $this->globalUsername,
                    $titleData
                );
            }

            $counter++;
        }

        $html .= $this->endTable();
        return $html;
    }
}
