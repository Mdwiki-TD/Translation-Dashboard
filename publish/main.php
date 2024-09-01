<?php
include_once __DIR__ . '/../auth/config.php';
include_once __DIR__ . '/../auth/helps.php';
include_once __DIR__ . '/../auth/send_edit.php';

include_once __DIR__ . '/helps.php';
include_once __DIR__ . '/fix_refs.php';
include_once __DIR__ . '/add_to_db.php';

use function OAuth\SendEdit\auth_do_edit;
use function Publish\Helps\get_access_from_db;
use function Publish\FixRefs\fix_wikirefs;
use function Publish\AddToDb\InsertPageTarget;

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
    $editit = ['edit' => ['error' => 'no access'], 'username' => $user];
    // exit(1);
} else {
    $access_key = $access['access_key'];
    $access_secret = $access['access_secret'];
    // ---
    $text = fix_wikirefs($text, $lang);
    // ---
    $editit = auth_do_edit($title, $text, $summary, $lang, $access_key, $access_secret);
    // ---
    $Success = $editit['edit']['result'] ?? '';
    // ---
    if ($Success === 'Success') {
        InsertPageTarget($sourcetitle, 'lead', $cat, $lang, $user, false, $title);
    };
    // ---
}
print(json_encode($editit, JSON_PRETTY_PRINT));

file_put_contents(__DIR__ . '/editit.json', json_encode($editit, JSON_PRETTY_PRINT));
