<?php

namespace Actions\WikiApi;
/*
Usage:
use function Actions\WikiApi\make_view_by_number;
use function Actions\WikiApi\get_views;
*/

$usr_agent = "WikiProjectMed Translation Dashboard/1.0 (https://mdwiki.toolforge.org/; tools.mdwiki@toolforge.org)";

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
    $start2 = !empty($pupdate) ? str_replace('-', '', $pupdate) : '20190101';

    $url2 = 'https://wikimedia.org/api/rest_v1/metrics/pageviews/per-article/' . $lang . '.wikipedia/all-access/all-agents/' . rawurlencode($target) . '/daily/' . $start2 . '/2030010100';

    $link = "<a target='_blank' href='$url'>$numb2</a>";

    if ($numb2 == '?' || $numb2 == 0 || $numb2 == '0') {
        $link = "<a target='_blank' name='toget' hrefjson='$url2' href='$url'>$numb2</a>";
    };
    return $link;
};
