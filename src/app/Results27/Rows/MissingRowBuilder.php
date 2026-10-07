<?php

namespace App\Results27\Rows;

use App\Utils\HtmlUrls;
use App\Utils\TrLink;

/**
 * Builds a single row for the Missing results table.
 */
class MissingRowBuilder
{
    public function build(
        string $title,
        string $traType,
        int $counter,
        string $langCode,
        string $cat,
        string $campaign,
        bool $isFullRow,
        bool $fullTrUser,
        ?string $globalUsername,
        array $titleData
    ): string {
        if (empty($traType)) {
            $traType = "lead";
        }

        $isVideo = str_starts_with(strtolower($title), "video:");
        if ($isVideo) {
            $traType = "all";
        }

        $words      = $titleData["w_lead_words"] ?? 0;
        $refs       = $titleData["r_lead_refs"] ?? 0;
        $importance = $titleData["importance"] ?? "Unknown";
        $enViews    = $titleData["en_views"] ?? "";
        $qid        = $titleData["qid"] ?? "";

        if ($traType === "all") {
            $words = $titleData["w_all_words"] ?? 0;
            $refs  = $titleData["r_all_refs"] ?? 0;
        }

        if (empty($importance)) {
            $importance = "Unknown";
        }

        $qidUrl    = HtmlUrls::make_wikidata_url_blank($qid);
        $mdwikiUrl = HtmlUrls::make_mdwiki_href($title);
        $buttons = "";

        // Translate buttons
        if (empty($globalUsername)) {
            $buttons = <<<HTML
                <a role="button" class="btn btn-outline-primary" href="/auth/login.php">
                    <i class="fas fa-sign-in-alt fa-sm fa-fw mr-1"></i>
                    <span class="navtitles">Login</span>
                </a>
            HTML;
        } else {
            $fullWords = $titleData["w_all_words"] ?? 0;
            $fullUrl = TrLink::makeTrLinkMedwiki($title, $langCode, $cat, $campaign, "all", $fullWords);
            $leadUrl = TrLink::makeTrLinkMedwiki($title, $langCode, $cat, $campaign, $traType, $words);

            if ($fullTrUser && !$isVideo) {
                $buttons = <<<HTML
                    <div class="inline">
                        <a href="{$leadUrl}" class="btn btn-outline-primary btn-sm" target="_blank">Lead</a>
                        <a href="{$fullUrl}" class="btn btn-outline-primary btn-sm" target="_blank">Full</a>
                    </div>
                HTML;
            } else {
                $buttons = "<a href='{$leadUrl}' class='btn btn-outline-primary btn-sm' target='_blank'>Translate</a>";
            }
        }

        $displayCounter = ($isFullRow && !$isVideo) ? "{$counter}.Full" : $counter;

        return <<<HTML
            <tr>
                <th class="num" scope="row">{$displayCounter}</th>
                <td class="link_container">
                    <a target="_blank" href="{$mdwikiUrl}">{$title}</a>
                </td>
                <th>{$buttons}</th>
                <td class="num" style="text-align:left">{$enViews}</td>
                <td class="num" style="text-align:left">{$importance}</td>
                <td class="num" style="text-align:left">{$words}</td>
                <td class="num" style="text-align:left">{$refs}</td>
                <td>{$qidUrl}</td>
            </tr>
        HTML;
    }
}
