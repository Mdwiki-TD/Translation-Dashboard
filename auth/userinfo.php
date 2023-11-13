<?php

// Require the library and set up the classes we're going to use in this second part of the demo.
require_once __DIR__ . '/../vendor/autoload.php';

use MediaWiki\OAuthClient\Client;
use MediaWiki\OAuthClient\ClientConfig;
use MediaWiki\OAuthClient\Consumer;
use MediaWiki\OAuthClient\Token;


// Get the wiki URL and OAuth consumer details from the config file.
require_once __DIR__ . '/config.php';

// Configure the OAuth client with the URL and consumer details.
$conf = new ClientConfig($oauthUrl);
$conf->setConsumer(new Consumer($consumerKey, $consumerSecret));
$conf->setUserAgent('DemoApp MediaWikiOAuthClient/1.0');
$client = new Client($conf);

// Get the Request Token's details from the session and create a new Token object.
session_start();
// Load the Access Token from the session.
// session_start();
$accessToken = new Token(
	$_SESSION['access_key'],
	$_SESSION['access_secret']
);

// Example 1: get the authenticated user's identity.
$ident = $client->identify($accessToken);
echo "You are authenticated as $ident->username.\n\n";

$_SESSION['username'] = $ident->username;

//---
require_once __DIR__ . "/mdwiki_sql.php";
//---
if ($ident->username != '') {
    sql_add_user($ident->username, '', '', '', '');
};
//---
// Example 2: do a simple API call.
$userInfo = json_decode($client->makeOAuthCall(
	$accessToken,
	"$apiUrl?action=query&meta=userinfo&uiprop=rights&format=json"
));
echo "== User info ==<br><br>";
print_r($userInfo);

// Example 3: make an edit (getting the edit token first).
# automatic redirect to edit.php
// header( 'Location: edit.php' );
