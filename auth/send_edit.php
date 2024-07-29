<?php

include_once __DIR__ . '/../vendor_load.php';

use MediaWiki\OAuthClient\Client;
use MediaWiki\OAuthClient\ClientConfig;
use MediaWiki\OAuthClient\Consumer;
use MediaWiki\OAuthClient\Token;

// Output the demo as plain text, for easier formatting.
// header( 'Content-type: text/plain' );

// Get the wiki URL and OAuth consumer details from the config file.
include_once __DIR__ . '/config.php';
include_once __DIR__ . '/helps.php';

function get_edits_token($client, $accessToken, $apiUrl)
{
    // Example 3: make an edit (getting the edit token first).
    $editToken = json_decode($client->makeOAuthCall(
        $accessToken,
        "$apiUrl?action=query&meta=tokens&format=json"
    ))->query->tokens->csrftoken;
    //---
    return $editToken;
}

function do_edit($title, $text, $summary, $wiki)
{
    global $gUserAgent, $consumerKey, $consumerSecret;
    // ---
    $oauthUrl = "https://$wiki.wikipedia.org/w/index.php?title=Special:OAuth";
    $apiUrl = "https://$wiki.wikipedia.org/w/api.php";
    // ---
    // Configure the OAuth client with the URL and consumer details.
    $conf = new ClientConfig($oauthUrl);
    $conf->setConsumer(new Consumer($consumerKey, $consumerSecret));
    $conf->setUserAgent($gUserAgent);
    $client = new Client($conf);
    // ---
    $access_key = get_from_cookie('access_key');
    $access_secret = get_from_cookie('access_secret');
    $accessToken = new Token($access_key, $access_secret);
    // ---
    $editToken = get_edits_token($client, $accessToken, $apiUrl);
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
    $editResult = json_decode($client->makeOAuthCall(
        $accessToken,
        $apiUrl,
        true,
        $apiParams
    ));
    // ---
    return $editResult;
}
