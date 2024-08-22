<?php
include_once __DIR__ . '/../auth/config.php';
include_once __DIR__ . '/../auth/helps.php';
include_once __DIR__ . '/../auth/send_edit.php';

include_once __DIR__ . '/helps.php';
include_once __DIR__ . '/send_edit.php';
include_once __DIR__ . '/text.php';

use function Publish\Helps\get_access_from_db;
use function Publish\Edit\send_edit;
use function OAuth\Helps\get_from_cookie;
use function Publish\Text\get_medwiki_text;

/*
$t_Params = [
    'title' => $title->getPrefixedDBkey(),
    'text' => $wikitext,
    'user' => $user_name,
    'summary' => $summary,
    'target' => $params['to'],
    'sourcetitle' => $params['sourcetitle'],
];
*/

$title   = $_POST['title'] ?? '';
$sourcetitle   = $_POST['sourcetitle'] ?? '';
$user    = $_POST['user'] ?? '';
$lang    = $_POST['target'] ?? '';
$text    = $_POST['text'] ?? '';
$summary = $_POST['summary'] ?? '';

// $username = get_from_cookie('username');

$access = get_access_from_db($user);
if ($access == null) {
    $editit = ['error' => 'no access', 'username' => $user];
    // exit(1);
} else {
    $access_key = $access['access_key'];
    $access_secret = $access['access_secret'];
    // $text = get_medwiki_text($title);

    $editit = send_edit($title, $text, $summary, $lang, $access_key, $access_secret);

}
print(json_encode($editit, JSON_PRETTY_PRINT));

file_put_contents(__DIR__ . '/editit.json', json_encode($editit, JSON_PRETTY_PRINT));
