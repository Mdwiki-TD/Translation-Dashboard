<?php

namespace Results\GetResults2026\Rows;

use function TD\Render\Html\make_mdwiki_href;
use function TD\Render\Html\make_wikidata_url_blank;

use function Results\Helps\make_translate_urls;

function make_tds_rows_responsive($isFull, $tds)
{
    $mdwikiUrl = $tds["mdwiki_url"];
    $counter    = $tds["cnt"];
    $tab    = $tds["tab"];
    $traType = $tds["tra_type"] ?? "";
    $pviews = $tds["pageviews"];
    $asse   = $tds["asse"];
    $words  = $tds["words"];
    $refs   = $tds["refs"];
    $qid    = $tds["qid"];
    $title  = $tds["title"];
    $_user_ = $tds["user"];
    $_date_ = $tds["date"];

    $cnt2 = $isFull && (strtolower(substr($title, 0, 6)) != 'video:') ? "$counter.Full" : $counter;

    $tdRows = <<<HTML
        <tr>
            <th class='num' scope="row">
                $cnt2
            </th>
            <td class='link_container'>
                <a target='_blank' href='$mdwikiUrl'>$title</a>
            </td>
            <th>
                $tab
            </th>
            <td style="text-align:center">
                $traType
            </td>
            <td class='num' style="text-align: left">
                $pviews
            </td>
            <td class='num' style="text-align: left">
                $asse
            </td>
            <td class='num' style="text-align: left">
                $words
            </td>
            <td class='num' style="text-align: left">
                $refs
            </td>
            <td>
                $qid
            </td>
            <td>
                $_user_
            </td>
            <td>
                $_date_
            </td>
        </tr>
    HTML;
    return $tdRows;
}

function make_one_row_new_inprocess(
    $title,
    $traType,
    $counter,
    $langCode,
    $cat,
    $camp,
    $inprocessTable,
    $inProgressButton,
    $isFull,
    $fullTrUser,
    $globalUsername,
    $titleData,
    $endpoint,
    $userCoord
) {

    // inprocess_table = { "title": "Andes virus infection", "user": "Mr. Ibrahem", "lang": "ar", "cat": "RTT", "translate_type": "all", "word": 0, "add_date": "2026-05-21 00:00:00", "campaign": "Main", "autonym": "العربية" }
    $_user_ = $inprocessTable['user'] ?? '';
    $_date_ = $inprocessTable['date'] ?? $inprocessTable['add_date'] ?? '';

    $word     = $titleData['w_lead_words'] ?? 0;
    $refs     = $titleData['r_lead_refs'] ?? 0;
    $importance = $titleData['importance'] ?? "";
    $enViews = $titleData['en_views'] ?? "";
    $qid      = $titleData['qid'] ?? "";

    if ($traType == 'all') {
        $word  = $titleData['w_all_words'] ?? 0;
        $refs  = $titleData['r_all_refs'] ?? 0;
    }

    if (empty($importance)) $importance = 'Unknown';

    $qidUrl = make_wikidata_url_blank($qid);

    $loginUserIsTheTranslator = (!empty($globalUsername) && $_user_ == $globalUsername) || $userCoord;

    $mdwikiUrl = make_mdwiki_href($title);

    [$tab, $translateUrl, $_] = make_translate_urls(
        $title,
        $traType,
        $word,
        $langCode,
        $cat,
        $camp,
        true,
        $inProgressButton,
        $_user_,
        $fullTrUser,
        $loginUserIsTheTranslator,
        $endpoint
    );

    // if $_date_ has : then split before first space
    if (strpos($_date_, ':') !== false) {
        $_date_ = explode(' ', $_date_)[0];
    };

    if (empty($globalUsername)) {
        $tab = "";
    }

    $tds = [
        "in_progress_translation_button" => $inProgressButton,
        "translate_url" => $translateUrl,
        "mdwiki_url" => $mdwikiUrl,
        "cnt" => $counter,
        "title" => $title,
        "tab" => $tab,
        "tra_type" => $traType,
        "pageviews" => $enViews,
        "asse" => $importance,
        "words" => $word,
        "refs" => $refs,
        "qid" => $qidUrl,
        "user" => $_user_,
        "date" => $_date_
    ];
    return make_tds_rows_responsive($isFull, $tds);
}
