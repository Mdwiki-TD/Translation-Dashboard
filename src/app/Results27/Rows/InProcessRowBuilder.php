<?php

namespace App\Results27\Rows;

use App\Utils\HtmlUrls;
use App\Utils\Helps;

/**
 * Builds a single row for the In-process results table.
 * Replace old make_one_row_new_inprocess function.
 */
class InProcessRowBuilder
{
    public function build(
        string $title,
        string $traType,
        int $counter,
        string $langCode,
        string $cat,
        string $campaign,
        array $inProcessData,
        bool $inProgressButton,
        bool $isFullRow,
        bool $fullTrUser,
        ?string $globalUsername,
        array $titleData,
        bool $userCoord
    ): string {
        // inProcessData = { "title": "Andes virus infection", "user": "Mr. Ibrahem", "lang": "ar", "cat": "RTT", "translate_type": "all", "word": 0, "add_date": "2026-05-21 00:00:00", "campaign": "Main", "autonym": "العربية" }
        $user = $inProcessData["user"] ?? "";
        $date = $inProcessData["date"] ?? $inProcessData["add_date"] ?? "";

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

        $loginUserIsTranslator = (!empty($globalUsername) && $user === $globalUsername) || $userCoord;

        [$buttons, $_, $_] = Helps::make_translate_urls(
            $title,
            $traType,
            $words,
            $langCode,
            $cat,
            $campaign,
            true,
            $inProgressButton,
            $user,
            $fullTrUser,
            $loginUserIsTranslator,
        );

        // Keep only the date part if datetime is present
        // if $_date_ has : then split before first space
        if (str_contains($date, ":")) {
            $date = explode(" ", $date)[0];
        }

        if (empty($globalUsername)) {
            $buttons = "";
        }

        $isVideo = str_starts_with(strtolower($title), "video:");
        $displayCounter = ($isFullRow && !$isVideo) ? "{$counter}.Full" : $counter;

        return <<<HTML
            <tr>
                <th class="num" scope="row">{$displayCounter}</th>
                <td class="link_container">
                    <a target="_blank" href="{$mdwikiUrl}">{$title}</a>
                </td>
                <th>{$buttons}</th>
                <td style="text-align:center">{$traType}</td>
                <td class="num" style="text-align:left">{$enViews}</td>
                <td class="num" style="text-align:left">{$importance}</td>
                <td class="num" style="text-align:left">{$words}</td>
                <td class="num" style="text-align:left">{$refs}</td>
                <td>{$qidUrl}</td>
                <td>{$user}</td>
                <td>{$date}</td>
            </tr>
        HTML;
    }
}
