<?php
//---
if (isset($_REQUEST['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};
//---
// Require the library and set up the classes we're going to use in this first part.
include_once __DIR__ . '/u.php';

include_once __DIR__ . '/../vendor_load.php';

use MediaWiki\OAuthClient\Client;
use MediaWiki\OAuthClient\ClientConfig;
use MediaWiki\OAuthClient\Consumer;

// Make sure the config file exists. This is just to make sure the demo makes sense if someone loads
// it in the browser without reading the documentation.
$configFile = __DIR__ . '/config.php';
if (!file_exists($configFile)) {
    echo "Configuration could not be read. Please create $configFile by copying config.dist.php";
    exit(1);
}

// Get the wiki URL and OAuth consumer details from the config file.
include_once $configFile;

// Configure the OAuth client with the URL and consumer details.
$conf = new ClientConfig($oauthUrl);
$conf->setConsumer(new Consumer($consumerKey, $consumerSecret));
$conf->setUserAgent($gUserAgent);
$client = new Client($conf);

function make_callback_url($url)
{
    $state = array();
    // ?action=login&cat=RTT&depth=1&code=&type=lead

    $return_to = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    //---
    if ($return_to != '') {
        $state['return_to'] = $return_to;
    }
    //---
    foreach (['cat', 'code', 'type', 'test', 'doit'] as $key) {
        $da = $_GET[$key] ?? '';
        if ($da != '') {
            $state[$key] = $da;
        }
    };
    //---
    $sta = "";
    if (!empty($state)) {
        $sta = '&' . http_build_query($state);
    }
    //---
    // echo $sta;
    //---
    $oauth_call = $url . $sta;
    //---
    return $oauth_call;
}
// ---
$call_back_url = make_callback_url('https://mdwiki.toolforge.org/Translation_Dashboard/auth.php?a=callback');
// ---
$client->setCallback($call_back_url);

// Send an HTTP request to the wiki to get the authorization URL and a Request Token.
// These are returned together as two elements in an array (with keys 0 and 1).
list($authUrl, $token) = $client->initiate();

// Store the Request Token in the session. We will retrieve it from there when the user is sent back
// from the wiki (see demo/callback.php).
session_start();
$_SESSION['request_key'] = $token->key;
$_SESSION['request_secret'] = $token->secret;

// Redirect the user to the authorization URL. This is usually done with an HTTP redirect, but we're
// making it a manual link here so you can see everything in action.
echo "Go to this URL to authorize this demo:<br /><a href='$authUrl'>$authUrl</a>";
header("Location: $authUrl");
