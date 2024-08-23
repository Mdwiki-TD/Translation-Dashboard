<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");

include_once __DIR__ . '/../vendor_load.php';
include_once __DIR__ . '/../auth/config.php';
include_once __DIR__ . '/../auth/helps.php';
include_once __DIR__ . '/helps.php';

use function Publish\Helps\get_access_from_db;
use MediaWiki\OAuthClient\Client;
use MediaWiki\OAuthClient\ClientConfig;
use MediaWiki\OAuthClient\Consumer;
use MediaWiki\OAuthClient\Token;

function get_client($wiki)
{
    global $gUserAgent, $consumerKey, $consumerSecret;
    // ---
    $oauthUrl = "https://$wiki.wikipedia.org/w/index.php?title=Special:OAuth";
    // ---
    // Configure the OAuth client with the URL and consumer details.
    $conf = new ClientConfig($oauthUrl);
    $conf->setConsumer(new Consumer($consumerKey, $consumerSecret));
    $conf->setUserAgent($gUserAgent);
    $client = new Client($conf);
    // ---
    return $client;
}
function get_csrftoken($client, $accessToken, $apiUrl)
{
    $response = $client->makeOAuthCall($accessToken, "$apiUrl?action=query&meta=tokens&format=json");
    // ---
    $data = json_decode($response);
    // ---
    if ($data == null || !isset($data->query->tokens->csrftoken)) {
        // Handle error
        echo "<br>get_csrftoken Error: " . json_last_error() . " " . json_last_error_msg();
        return null;
    }
    // ---
    return $data->query->tokens->csrftoken;
}

function get_cxtoken($wiki, $access_key, $access_secret)
{
    // ---
    $apiUrl = "https://$wiki.wikipedia.org/w/api.php";
    // ---
    $client = get_client($wiki);
    // ---
    $accessToken = new Token($access_key, $access_secret);
    // ---
    $csrftoken = get_csrftoken($client, $accessToken, $apiUrl);
    // ---
    if ($csrftoken == null) {
        return json_encode(['error' => 'get_csrftoken failed']);
    }
    // ---
    $apiParams = [
        'action' => 'cxtoken',
        'token' => $csrftoken,
        'format' => 'json',
    ];
    // ---
    $response = $client->makeOAuthCall($accessToken, $apiUrl, true, $apiParams);
    // ---
    $editResult = json_decode($response);
    // ---
    if ($editResult == null || isset($editResult->error)) {
        echo "<br>get_cxtoken: Error: " . json_last_error() . " " . json_last_error_msg();
    }
    // ---
    return $editResult;
}

$wiki    = $_GET['wiki'] ?? '';
$user    = $_GET['user'] ?? '';

if ($wiki == '' || $user == '') {
    print(json_encode(['error' => 'wiki or user is empty']));
    exit(1);
}

$access = get_access_from_db($user);

if ($access == null) {
    $cxtoken = ['error' => 'no access', 'username' => $user];
    // exit(1);
} else {
    $access_key = $access['access_key'];
    $access_secret = $access['access_secret'];
    // $text = get_medwiki_text($title);

    $cxtoken = get_cxtoken($wiki, $access_key, $access_secret);
}

print(json_encode($cxtoken, JSON_PRETTY_PRINT));
