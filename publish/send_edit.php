<?php

namespace Publish\Edit;
/*
Usage:
use function Publish\Edit\send_edit;
*/

include_once __DIR__ . '/../vendor_load.php';
include_once __DIR__ . '/../auth/config.php';
include_once __DIR__ . '/../auth/helps.php';

use MediaWiki\OAuthClient\Client;
use MediaWiki\OAuthClient\ClientConfig;
use MediaWiki\OAuthClient\Consumer;
use MediaWiki\OAuthClient\Token;
use function OAuth\Helps\get_from_cookie;

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
function get_edits_token($client, $accessToken, $apiUrl)
{
    $response = $client->makeOAuthCall($accessToken, "$apiUrl?action=query&meta=tokens&format=json");
    // ---
    $data = json_decode($response);
    // ---
    if ($data == null || !isset($data->query->tokens->csrftoken)) {
        // Handle error
        echo "<br>get_edits_token Error: " . json_last_error() . " " . json_last_error_msg();
        return null;
    }
    // ---
    return $data->query->tokens->csrftoken;
}


function make_edit($title, $text, $summary, $wiki, $access_key, $access_secret)
{
    // ---
    $apiUrl = "https://$wiki.wikipedia.org/w/api.php";
    // ---
    $client = get_client($wiki);
    // ---
    $accessToken = new Token($access_key, $access_secret);
    // ---
    $editToken = get_edits_token($client, $accessToken, $apiUrl);
    // ---
    if ($editToken == null) {
        return null;
    }
    // ---
    $apiParams = [
        'action' => 'edit',
        'title' => $title,
        // 'section' => 'new',
        'summary' => $summary,
        'text' => $text,
        'token' => $editToken,
        'format' => 'json',
    ];
    // ---
    $response = $client->makeOAuthCall($accessToken, $apiUrl, true, $apiParams);
    // ---
    $editResult = json_decode($response);
    // ---
    if ($editResult == null || isset($editResult->error)) {
        // Handle error
        echo "<br>make_edit: Error: " . json_last_error() . " " . json_last_error_msg();
        return null;
    }
    // ---
    return $editResult;
}

function send_edit($title, $text, $summary, $wiki)
{
    // ---
    if ($wiki == '' || $title == '' || $text == '') {
        return 'error:  wiki or title or text is empty';
    }
    // ---
    $access_key = get_from_cookie('accesskey');
    $access_secret = get_from_cookie('access_secret');
    // ---
    if ($access_key == '' || $access_secret == '') {
        return 'error:  access_key or access_secret is empty';
    }
    // ---
    return make_edit($title, $text, $summary, $wiki, $access_key, $access_secret);
}
