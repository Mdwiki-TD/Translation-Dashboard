<?php

namespace Results\GetResults2026\Rows;

use function Results\TrLink\make_ContentTranslation_url;
use function TD\Render\Html\make_mdwiki_article_url_blank;
use function TD\Render\Html\make_wikipedia_url_blank;
use function TD\Render\Html\make_wikidata_url_blank;

/**
 * Builds a single row for the Exists results table.
 */
class ExistsRowBuilder
{
    public function build(
        string $title,
        int $counter,
        string $langCode,
        string $cat,
        string $camp,
        array $titleData,
        ?string $globalUsername,
        bool $userCoord,
        string $endpoint
    ): string {
        $importance = $titleData['importance'] ?? 'Unknown';
        $qid        = $titleData['qid'] ?? '';
        $target     = $titleData['target'] ?? '';
        $via        = $titleData['via'] ?? 'before';

        $mdwikiLink = make_mdwiki_article_url_blank($title);
        $qidUrl     = make_wikidata_url_blank($qid);

        $targetTd  = '';
        $targetTd2 = '';

        if ($target) {
            if ($via === 'td') {
                $targetTd = make_wikipedia_url_blank($target, $langCode);
            } else {
                $targetTd2 = make_wikipedia_url_blank($target, $langCode);
            }
        }

        $translateButton = '';
        if (!empty($globalUsername) && $userCoord) {
            $translateUrl = make_ContentTranslation_url(
                $title,
                $langCode,
                $cat,
                $camp,
                'lead',
                $endpoint
            );
            $translateButton = <<<HTML
                <div class="inline">
                    <a href="{$translateUrl}" class="btn btn-outline-primary btn-sm" target="_blank">Translate</a>
                </div>
            HTML;
        }

        return <<<HTML
        <tr>
            <th class="" scope="row" style="text-align:center">{$counter}</th>
            <td class="link_container spannowrap">{$mdwikiLink}</td>
            <td>{$translateButton}</td>
            <td>{$targetTd}</td>
            <td>{$targetTd2}</td>
            <td>{$qidUrl}</td>
        </tr>
        HTML;
    }
}
