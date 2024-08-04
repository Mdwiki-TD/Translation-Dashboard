<?php
// https://cxserver.wikimedia.org/v2/page/en/ar/Hippocrates_(disambiguation)
header("Content-type: application/json");
header("Access-Control-Allow-Origin: http://localhost:300");

$usr_agent = 'WikiProjectMed Translation Dashboard/1.0 (https://mdwiki.toolforge.org/; tools.mdwiki@toolforge.org)';

function get_url_params_result(string $url): string
{
    global $usr_agent;
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
    curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
    curl_setopt($ch, CURLOPT_USERAGENT, $usr_agent);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);

    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

$title = $_GET['title'] ?? '';

$params = [
    "action" => "parse",
    "format" => "json",
    "page" => $title,
    "prop" => "text|revid",
    "parsoid" => 1,
    "utf8" => 1,
    "formatversion" => "2"
];

$text = "";
$revid = "";

if ($title != '') {
    $end_point = "https://mdwiki.org/w/api.php";
    // ---
    $url = $end_point . '?' . http_build_query($params);
    // ---
    try {
        $res = get_url_params_result($url);
        if ($res) {
            $data = json_decode($res, true);
            $text = $data['parse']['text'];
            $revid = $data['parse']['revid'];
        }
    } catch (Exception $e) {
    };
}


echo json_encode([
    "sourceLanguage" => "mdwiki",
    "title" => $title,
    "revision" => $revid,
    "segmentedContent" => $text
]);
