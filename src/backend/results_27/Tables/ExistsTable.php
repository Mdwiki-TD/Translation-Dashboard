<?php

namespace Results\GetResults2026\Tables;

use Results\GetResults2026\Rows\ExistsRowBuilder;

/**
 * Renders the table of already existing pages.
 */
class ExistsTable extends AbstractResultsTable
{
    private ExistsRowBuilder $rowBuilder;
    private string $langCode;
    private string $cat;
    private string $camp;
    private ?string $globalUsername;
    private bool $userCoord;
    private string $endpoint;

    public function __construct(
        string $langCode,
        string $cat,
        string $camp,
        ?string $globalUsername,
        bool $userCoord,
        string $endpoint
    ) {
        $this->rowBuilder     = new ExistsRowBuilder();
        $this->langCode       = $langCode;
        $this->cat            = $cat;
        $this->camp           = $camp;
        $this->globalUsername = $globalUsername;
        $this->userCoord      = $userCoord;
        $this->endpoint       = $endpoint;
    }

    public function render(array $items): string
    {
        $countTranslated       = 0;
        $countTranslatedBefore = 0;
        $rowsHtml              = '';
        $counter               = 1;

        foreach ($items as $title => $data) {
            if (empty($title)) {
                continue;
            }

            $title = str_replace('_', ' ', $title);

            if (($data['via'] ?? '') === 'td') {
                $countTranslated++;
            } else {
                $countTranslatedBefore++;
            }

            $rowsHtml .= $this->rowBuilder->build(
                $title,
                $counter,
                $this->langCode,
                $this->cat,
                $this->camp,
                $data,
                $this->globalUsername,
                $this->userCoord,
                $this->endpoint
            );

            $counter++;
        }

        return <<<HTML
        <table class="table compact table-striped table_100 table_text_left table_responsive display">
            <thead>
                <tr>
                    <th class="num">#</th>
                    <th class="spannowrap" style="text-align:center">Title</th>
                    <th>Translate</th>
                    <th>Translated ({$countTranslated})</th>
                    <th>Translated before ({$countTranslatedBefore})</th>
                    <th class="spannowrap" style="text-align:center">
                        <span data-bs-toggle="tooltip" data-bs-title="Wikidata identifier">Qid</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                {$rowsHtml}
            </tbody>
        </table>
        HTML;
    }
}
