<?php

// Require the library and set up the classes we're going to use in this second part of the demo.

include_once __DIR__ . '/../vendor_load.php';

use MediaWiki\OAuthClient\Client;
use MediaWiki\OAuthClient\ClientConfig;
use MediaWiki\OAuthClient\Consumer;
use MediaWiki\OAuthClient\Token;

use function Actions\MdwikiSql\sql_add_user;
// Get the wiki URL and OAuth consumer details from the config file.
include_once __DIR__ . '/config.php';

// Configure the OAuth client with the URL and consumer details.
$conf = new ClientConfig($oauthUrl);
$conf->setConsumer(new Consumer($consumerKey, $consumerSecret));
$conf->setUserAgent($gUserAgent);
$client = new Client($conf);

// Get the Request Token's details from the session and create a new Token object.
session_start();
// Load the Access Token from the session.
$accessToken = new Token(
	$_SESSION['access_key'],
	$_SESSION['access_secret']
);

// Example 1: get the authenticated user's identity.
$ident = $client->identify($accessToken);
// Use htmlspecialchars to properly encode the output and prevent XSS vulnerabilities.
echo "You are authenticated as " . htmlspecialchars($ident->username, ENT_QUOTES, 'UTF-8') . ".\n\n";
//---
$_SESSION['username'] = $ident->username;
//---
// include_once __DIR__ . "/../actions/mdwiki_sql.php";
// //---
// if ($ident->username != '') {
//     sql_add_user($ident->username, '', '', '', '');
// };
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
