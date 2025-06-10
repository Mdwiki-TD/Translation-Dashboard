<?php

namespace Actions\WikiApi;
/*
Usage:
use function Actions\WikiApi\make_view_by_number;
use function Actions\WikiApi\get_views;
*/

function make_view_by_number($target, $numb, $lang, $pupdate)
{
    // remove spaces and tab characters
    $target = trim($target);
    $numb2 = (!empty($numb)) ? $numb : "?";
    $start = !empty($pupdate) ? $pupdate : '2019-01-01';
    $end = date("Y-m-d", strtotime("yesterday"));

    $url = 'https://pageviews.wmcloud.org/?' . http_build_query(array(
        'project' => "$lang.wikipedia.org",
        'platform' => 'all-access',
        'agent' => 'all-agents',
        'start' => $start,
        'end' => $end,
        // 'range' => 'all-time',
        'redirects' => '0',
        'pages' => $target,
    ));
    // ---
    $numb3 = (is_numeric($numb2)) ? number_format($numb2) : $numb2;
    $link = "<a target='_blank' href='$url'>$numb3</a>";
    // ---
    if (is_numeric($numb2) && intval($numb2) > 0) {
        return $link;
    }
    // ---
    $start2 = !empty($pupdate) ? str_replace('-', '', $pupdate) : '20190101';
    // ---
    $url2 = 'https://wikimedia.org/api/rest_v1/metrics/pageviews/per-article/' . $lang . '.wikipedia/all-access/all-agents/' . rawurlencode($target) . '/daily/' . $start2 . '/2030010100';
    // ---
    $link = "<a target='_blank' name='toget' data-json-url='$url2' href='$url'>$numb2</a>";
    // ---
    return $link;
};
