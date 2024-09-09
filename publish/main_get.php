<?php
include_once __DIR__ . '/../auth/config.php';
include_once __DIR__ . '/../auth/helps.php';
include_once __DIR__ . '/../auth/send_edit.php';
include_once __DIR__ . '/../Tables/sql_tables.php'; // $sql_qids $cat_titles $cat_to_camp $camp_to_cat
include_once __DIR__ . '/add_to_db.php';
include_once __DIR__ . '/text.php';

use function OAuth\SendEdit\auth_do_edit;
use function OAuth\Helps\get_from_cookie;
use function Publish\AddToDb\InsertPageTarget;
use function Publish\Text\get_medwiki_text;

function open_fixwikirefs($target, $lang)
{
    // ----
    $params = [
        'save' => "1",
        'title' => $target,
        'lang' => $lang
    ];
    // ----
    $url = "https://mdwiki.toolforge.org/fixwikirefs.php?" . http_build_query($params);
    // ----
    // open new window
    echo <<<HTML
        \n
        <script type='text/javascript'>
            window.open('$url', '_blank');
        </script>
        \n
    HTML;
    // ----
}

function do_it($target, $text, $summary, $sourcetitle, $lang, $access_key, $access_secret)
{
    if (empty($summary)) {
        $summary = 'Created by translating the page [[:mdwiki:' . $sourcetitle . '|' . $sourcetitle . ']]. #mdwikicx .';
    }

    $editit = auth_do_edit($target, $text, $summary, $lang, $access_key, $access_secret);

    print(json_encode($editit, JSON_PRETTY_PRINT));

    $Success = $editit['edit']['result'] ?? '';

    echo <<<HTML
        <br>Success:$Success<br>
    HTML;

    return $Success;
}
function start_main_get()
{
    global $camp_to_cat;
    // ---
    $campaign = $_GET['campaign'] ?? '';
    $cat = $camp_to_cat[$campaign] ?? '';
    // ---
    $target  = $_GET['title'] ?? '';
    $lang    = $_GET['lang'] ?? '';
    $sourcetitle = $_GET['sourcetitle'] ?? '';
    $user    = $_GET['user'] ?? '';
    // ---
    $summary = $_GET['summary'] ?? '';
    $test    = $_GET['test'] ?? '';

    $username = get_from_cookie('username');
    if ($username != $user) {
        echo json_encode(['error' => 'no access', 'user' => $user]);
        exit(1);
    }
    // $access = get_access_from_db($user);

    $text = get_medwiki_text($target) ?? '';

    if (empty($text)) {
        // refresh the page
        header("Refresh:1; url={$_SERVER['PHP_SELF']}?title=$target&lang=$lang&summary=$summary&user=$user");
        exit();
    }

    $access_key = get_from_cookie('accesskey');
    $access_secret = get_from_cookie('access_secret');
    if (empty($access_key) || empty($access_secret)) {
        echo json_encode(['error' => 'log in first!', 'user' => $user]);
        exit(1);
    }
    $link = "https://{$lang}.wikipedia.org/w/index.php?title={$target}";

    echo <<<HTML
        <a href='$link' target='_blank'>{$target}</a><br>
        <textarea id='text' name='text' cols='80' rows='8'>{$text}</textarea>
        <br>
    HTML;


    $result = do_it($target, $text, $summary, $sourcetitle, $lang, $access_key, $access_secret);

    if ($result === 'Success') {
        InsertPageTarget($sourcetitle, 'lead', $cat, $lang, $user, $test, $target);
        // ----
        open_fixwikirefs($target, $lang);
        // ----
        if (!empty($test)) {
            echo <<<HTML
                < meta http-equiv="refresh" content="0; URL={$link}" />
            HTML;
        } else {
            echo <<<HTML
                <meta http-equiv="refresh" content="0; URL={$link}" />
            HTML;
        }
    };
}
