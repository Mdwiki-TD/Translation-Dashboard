<?php
//---
if (isset($_REQUEST['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};
//---
require_once __DIR__ . '/../vendor/autoload.php';

use MediaWiki\OAuthClient\Client;
use MediaWiki\OAuthClient\ClientConfig;
use MediaWiki\OAuthClient\Consumer;
use MediaWiki\OAuthClient\Token;

// Output the demo as plain text, for easier formatting.
// header( 'Content-type: text/plain' );

// Get the wiki URL and OAuth consumer details from the config file.
require_once __DIR__ . '/config.php';

// Configure the OAuth client with the URL and consumer details.
$conf = new ClientConfig( $oauthUrl );
$conf->setConsumer( new Consumer( $consumerKey, $consumerSecret ) );
$conf->setUserAgent( $gUserAgent );
$client = new Client( $conf );

// Load the Access Token from the session.
session_start();
$accessToken = new Token( $_SESSION['access_key'], $_SESSION['access_secret'] );

// Example 1: get the authenticated user's identity.
$ident = $client->identify( $accessToken );

function get_edit_token(){
    global $client, $accessToken, $apiUrl;
    // Example 3: make an edit (getting the edit token first).
    $editToken = json_decode( $client->makeOAuthCall(
        $accessToken,
        "$apiUrl?action=query&meta=tokens&format=json"
    ) )->query->tokens->csrftoken;
    //---
    return $editToken;
}

function doApiQuery($Params, $addtoken = null){
    global $client, $accessToken, $apiUrl;
    //---
    if ($addtoken !== null) $Params['token'] = get_edit_token();    
    //---
    $Result = $client->makeOAuthCall(
        $accessToken,
        $apiUrl,
        true,
        $Params
    );
    //---
    return json_decode($Result, true);
}

$post = $_GET;
if (isset($post['action'])) {
    $result = doApiQuery($post);
    echo json_encode($result, true);
}