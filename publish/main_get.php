<?php
include_once __DIR__ . '/../auth/config.php';
include_once __DIR__ . '/../auth/helps.php';
include_once __DIR__ . '/../auth/send_edit.php';

// include_once __DIR__ . '/helps.php';
// include_once __DIR__ . '/send_edit.php';

// use function Publish\Helps\get_access_from_db;
// use function Publish\Edit\send_edit;
use function OAuth\Helps\get_from_cookie;

function get_medwiki_text($title)
{
    $params = [
        'title' => $title,
        'action' => 'raw',
    ];
    $endPoint = "https://medwiki.toolforge.org/md/index.php?";

    $usr_agent = "WikiProjectMed Translation Dashboard/1.0 (https://mdwiki.toolforge.org/; tools.mdwiki@toolforge.org)";

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $endPoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
    // curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
    curl_setopt($ch, CURLOPT_USERAGENT, $usr_agent);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);

    $output = curl_exec($ch);
    $url = "{$endPoint}?" . http_build_query($params);
    if ($output === FALSE) {
        echo ("<br>cURL Error: " . curl_error($ch) . "<br>$url");
    }

    curl_close($ch);
    return $output;
}

$title   = $_GET['title'] ?? '';
$lang    = $_GET['lang'] ?? '';
$summary = $_GET['summary'] ?? '';
$sourcetitle = $_GET['sourcetitle'] ?? '';
$user    = $_GET['user'] ?? '';

$username = get_from_cookie('username');
if ($username != $user) {
    echo json_encode(['error' => 'no access', 'user' => $user]);
    exit(1);
}
// $access = get_access_from_db($user);

$text = get_medwiki_text($title) ?? '';

if ($text === '') {
    // refresh the page
    header("Refresh:1; url={$_SERVER['PHP_SELF']}?title=$title&lang=$lang&summary=$summary&user=$user");
    exit();
}

$access_key = get_from_cookie('accesskey');
$access_secret = get_from_cookie('access_secret');

$link = "https://{$lang}.wikipedia.org/w/index.php?title={$title}";

echo <<<HTML
    <a href='$link' target='_blank'>{$title}</a><br>
    <textarea id='text' name='text' cols='80' rows='8'>{$text}</textarea>
    <br>
HTML;
if ($summary === "") {
    $summary = 'Created by translating the page [[:mdwiki:' . $sourcetitle . '|' . $sourcetitle . ']]. #mdwikicx .';
}
$editit = do_edit($title, $text, $summary, $lang, $access_key, $access_secret);

print(json_encode($editit, JSON_PRETTY_PRINT));

$Success = $editit['edit']['result'] ?? '';

echo <<<HTML
    <br>Success:$Success<br>
HTML;

if ($Success === 'Success') {
    echo <<<HTML
        <meta http-equiv="refresh" content="0; URL={$link}" />
HTML;
};
