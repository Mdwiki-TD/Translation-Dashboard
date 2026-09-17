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
        $isFullMode = ($this->traType === 'all');

        // Sort by English page views (descending)
        usort($items, static function (array $a, array $b): int {
            return ($b["en_views"] ?? 0) <=> ($a["en_views"] ?? 0);
        });

        // { "title": "11p deletion syndrome", "category": "RTT", "importance": "", "r_lead_refs": 5, "r_all_refs": 14, "en_views": 838, "w_lead_words": 221, "w_all_words": 547, "qid": "Q1892153", "target": "متلازمة واجر" }
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

            // Skip lead filtering when full translation applies or user is allowed full access
            if ($isFullMode || $this->fullTrUser) {
                $html .= $row;
                $counter++;
                continue;
            }

            // if title in no_lead_translates array then $noLead = true
            $noLead = (in_array($title, $this->noLeadTranslates)) ? true : false;

            // if title in full_translates array then $TitleCanFullTranslated = true
            $TitleCanFullTranslated   = in_array($title, $this->fullTranslates, true);

            if ($noLead && !$TitleCanFullTranslated) {
                continue;
            }

            if (!$noLead) {
                $html .= $row;
            }

            if ($TitleCanFullTranslated) {
                $row = $this->rowBuilder->build(
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
                $html .= $row;
            }

            $counter++;
        }

        $html .= $this->endTable();
        return $html;
    }
}
