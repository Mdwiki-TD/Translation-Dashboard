<?php

namespace Publish\Text;

/*

use function Publish\Text\get_medwiki_text;

*/

require_once __DIR__ . '/../actions/curl_api.php';

use function Actions\CurlApi\post_url_params_result;

function get_medwiki_text($target)
{
    $params = [
        'title' => $target,
        'action' => 'raw',
    ];
    $endPoint = "https://medwiki.toolforge.org/md/index.php?";

    $result = post_url_params_result($endPoint, $params);

    return $result;
}
