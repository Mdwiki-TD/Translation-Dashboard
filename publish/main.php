<?php
if (isset($_REQUEST['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};
include_once __DIR__ . '/../auth/config.php';
include_once __DIR__ . '/../auth/helps.php';
include_once __DIR__ . '/../auth/send_edit.php';

include_once __DIR__ . '/../actions/functions.php';
include_once __DIR__ . '/helps.php';
// include_once __DIR__ . '/fix_refs.php';
include_once __DIR__ . '/add_to_db.php';
include_once __DIR__ . '/wd.php';
include_once __DIR__ . '/../Tables/sql_tables.php'; // $sql_qids $cat_titles $cat_to_camp $camp_to_cat

use function Actions\Functions\test_print;
use function OAuth\SendEdit\auth_do_edit;
use function Publish\Helps\get_access_from_db;
// use function Publish\FixRefs\fix_wikirefs;
use function Publish\AddToDb\InsertPageTarget;
use function Publish\AddToDb\retrieveCampaignCategories;
use function Publish\WD\LinkToWikidata;

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

$sourcetitle   = $_REQUEST['sourcetitle'] ?? '';
$title    = $_REQUEST['title'] ?? '';
$user     = $_REQUEST['user'] ?? '';
$lang     = $_REQUEST['target'] ?? '';
$text     = $_REQUEST['text'] ?? '';
$summary  = $_REQUEST['summary'] ?? '';
$campaign = $_REQUEST['campaign'] ?? '';

// $username = get_from_cookie('username');

$access = get_access_from_db($user);

if ($access == null) {
    $editit = ['edit' => ['error' => 'no access'], 'username' => $user];
} else {
    $access_key = $access['access_key'];
    $access_secret = $access['access_secret'];
    // ---
    // $text = fix_wikirefs($text, $lang);
    // ---
    $editit = auth_do_edit($title, $text, $summary, $lang, $access_key, $access_secret);
    // ---
    $Success = $editit['edit']['result'] ?? '';
    // ---
    $tab = [
        'title' => $title,
        'summary' => $summary,
        'lang' => $lang,
        'user' => $user,
        'campaign' => $campaign,
        'result' => $Success,
        'sourcetitle' => $sourcetitle

    ];
    try {
        // dump $tab to file in folder to_do
        $file_name = __DIR__ . '/to_do/' . rand(0, 999999999) . '.json';
        if (!is_dir(__DIR__ . '/to_do')) {
            mkdir(__DIR__ . '/to_do', 0755, true);
        }
        file_put_contents($file_name, json_encode($tab, JSON_PRETTY_PRINT));
    } catch (Exception $e) {
        test_print($e->getMessage());
    }
    // ---
    if ($Success === 'Success') {
        // ---
        $camp_to_cat = retrieveCampaignCategories();
        $cat      = $camp_to_cat[$campaign] ?? '';
        // ---
        try {
            InsertPageTarget($sourcetitle, 'lead', $cat, $lang, $user, "", $title);
            $editit['LinkToWikidata'] = LinkToWikidata($sourcetitle, $lang, $user, $title, $access_key, $access_secret);
        } catch (Exception $e) {
            test_print($e->getMessage());
        }
        // ---
    };
}

test_print("\n<br>");
test_print("\n<br>");

print(json_encode($editit, JSON_PRETTY_PRINT));

file_put_contents(__DIR__ . '/editit.json', json_encode($editit, JSON_PRETTY_PRINT));
