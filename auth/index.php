<?php
if (isset($_REQUEST['test'])) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
};
//---
// Require the library and set up the classes we're going to use in this second part of the demo.
require_once __DIR__ . '/../vendor/autoload.php';

use MediaWiki\OAuthClient\Client;
use MediaWiki\OAuthClient\ClientConfig;
use MediaWiki\OAuthClient\Consumer;

// Get the wiki URL and OAuth consumer details from the config file.
require_once __DIR__ . '/config.php';

// Configure the OAuth client with the URL and consumer details.
$conf = new ClientConfig( $oauthUrl );
$conf->setConsumer( new Consumer( $consumerKey, $consumerSecret ) );
$conf->setUserAgent( 'DemoApp MediaWikiOAuthClient/1.0' );
$client = new Client( $conf );

// Get the Request Token's details from the session and create a new Token object.
session_start();
//---
$username = $_SESSION['username'] ?? '';
//---
function echo_login() {
	global $username;
	if ($username == '') {
		echo <<<HTML
			Go to this URL to authorize this demo:<br />
			<a href='auth.php?a=login'>Login.php</a><br />
			
		HTML;
		echo __DIR__;
	} else {
		echo <<<HTML
			You are authenticated as $username.<br />
			Continue to <a href='auth.php?a=edit'>edit</a><br>
			<a href='auth.php?a=logout'>logout.php</a>
		HTML;
	};
	//---
};