<?php
if ($_GET['test'] != '') {
    // echo(__file__);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};
/**
1: login with doAuthorizationRedirect();
 * Written in 2013 by Brad Jorsch
 *
 * To the extent possible under law, the author(s) have dedicated all copyright 
 * and related and neighboring rights to this software to the public domain 
 * worldwide. This software is distributed without any warranty. 
 *
 * See <http://creativecommons.org/publicdomain/zero/1.0/> for a copy of the 
 * CC0 Public Domain Dedication.
 */

// ******************** CONFIGURATION ********************
//$oauth_callback = 'https://mdwiki.toolforge.org/Translation_Dashboard/index.php';
/**
 * Set this to point to a file (outside the webserver root!) containing the 
 * following keys:
 * - agent: The HTTP User-Agent to use
 * - consumerKey: The "consumer token" given to you when registering your app
 * - consumerSecret: The "secret token" given to you when registering your app
 */
 
/**
 * Set this to the Special:OAuth/authorize URL. 
 * To work around MobileFrontend redirection, use /wiki/ rather than /w/index.php.
 */
$mwOAuthAuthorizeUrl = 'https://mdwiki.org/wiki/Special:OAuth/authorize';

/**
 * Set this to the Special:OAuth URL. 
 * Note that /wiki/Special:OAuth fails when checking the signature, while
 * index.php?title=Special:OAuth works fine.
 */
$mwOAuthUrl = 'https://mdwiki.org/w/index.php?title=Special:OAuth';

/**
 * Set this to the API endpoint
 */
$apiUrl = 'https://mdwiki.org/w/api.php';

$twoYears = time() + 60 * 60 * 24 * 365 * 2;
/**
 * This should normally be "500". But Tool Labs insists on overriding valid 500
 * responses with a useless error page.
 */
$errorCode = 200;
$SCRIPT_NAME = htmlspecialchars( $_SERVER['SCRIPT_NAME'] ) ; 
// ****************** END CONFIGURATION ******************

// Setup the session cookie
session_name( 'OAuthHelloWorld' );
$params = session_get_cookie_params();
session_set_cookie_params(
    $params['lifetime'],
    dirname( $_SERVER['SCRIPT_NAME'] )
);
// ******************
// Read the ini file
$inifile_local = '../OAuthConfig.ini';
$inifile_mdwiki = '/data/project/mdwiki/OAuthConfig.ini';
// ******************
$inifile = $inifile_mdwiki;
// ******************
// $teste = file_get_contents($inifile_mdwiki);
// if ( $teste != '' ) { 
if ( strpos( __file__ , '/mnt/' ) === 0 ) {
    $inifile = $inifile_mdwiki;
} else {
    $inifile = $inifile_local;
};
// ******************
$ini = parse_ini_file( $inifile );
// ******************
if ( $ini === false ) {
    header( "HTTP/1.1 $errorCode Internal Server Error" );
    echo "The ini file:($inifile) could not be read";
    // exit(0);
}
if ( !isset( $ini['agent'] ) ||
    !isset( $ini['consumerKey'] ) ||
    !isset( $ini['consumerSecret'] )
) {
    header( "HTTP/1.1 $errorCode Internal Server Error" );
    echo 'Required configuration directives not found in ini file';
    exit(0);
}
$gUserAgent = $ini['agent'];
$gConsumerKey = $ini['consumerKey'];
$gConsumerSecret = $ini['consumerSecret'];

// Load the user token (request or access) from the session
//===
$username = '';
if (!isset($_GET['test1']) && $_SERVER['SERVER_NAME'] == 'localhost') { 
    $username = 'Mr. Ibrahem';
    // $username = '';
};
//===
if(isset($_COOKIE['username'])) { $username = $_COOKIE['username']; };
//===
$gTokenKey = '';
$gTokenSecret = '';
//===
session_start();
//===
if ( isset( $_SESSION['tokenKey'] ) ) {
    
    $gTokenKey = $_SESSION['tokenKey'];
    $gTokenSecret = $_SESSION['tokenSecret'];
    
} elseif ( isset( $_COOKIE['tokenKey'] ) ) {
    
    $gTokenKey    = $_COOKIE['tokenKey'];
    $gTokenSecret = $_COOKIE['tokenSecret'];
    
};
//===
session_write_close();
//===

// Fetch the access token if this is the callback from requesting authorization
// we get it after login
if ( isset( $_REQUEST['oauth_verifier'] ) && $_REQUEST['oauth_verifier'] ) {
    setcookie('oauth_verifier',$_REQUEST['oauth_verifier'],$twoYears,'/','mdwiki.toolforge.org',true,true);
    fetchAccessToken();
};
// };

//function login() {
//global $gTokenSecret,$username;
if ($gTokenSecret != '' and $gTokenKey != '') {
    //after fetchAccessToken();
    //print 'doIdentify';
    doIdentify('');
    };

//********************************
// Take any requested action
switch ( isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '' ) {
    case 'login':
        doAuthorizationRedirect();
        return;
        
    #case '':
        #doAuthorizationRedirect();
        #return;

    case 'logout':
        // session_unset();
        // unset cookies
		setcookie('username', '', time()-$twoYears,'/','mdwiki.toolforge.org',true,true);
		setcookie('OAuthHelloWorld', '', time()-$twoYears,'/','mdwiki.toolforge.org',true,true);
        if (isset($_SERVER['HTTP_COOKIE'])) {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach($cookies as $cookie) {
                $parts = explode('=', $cookie);
                $name = trim($parts[0]);
                // setcookie($name, '', time()-$twoYears);
                setcookie($name, '', time()-$twoYears,'/','mdwiki.toolforge.org',true,true);
            };
        };
        // session_start();
        
        unset($_COOKIE['username']);
        unset($_SESSION["tokenKey"]);
        unset($_SESSION["tokenSecret"]);
        unset($username);
        header("Location: index.php");
        exit;
        break;

    case 'identify':
        $ma = doIdentify('n');
        break;

}

// ******************** CODE ********************

/*
print "<li><a href='$SCRIPT_NAME?action=identify'>identify</a></li>";
if ( $username != '' ) {
    print "hi $username";
    print "<li><a href='$SCRIPT_NAME?action=logout'>Logout</a></li>";
} else {
    print "<li><a href='$SCRIPT_NAME?action=login'>Login</a></li>";
}
*/
//===
/**
 * Utility function to sign a request
 *
 * Note this doesn't properly handle the case where a parameter is set both in 
 * the query string in $url and in $params, or non-scalar values in $params.
 *
 * @param string $method Generally "GET" or "POST"
 * @param string $url URL string
 * @param array $params Extra parameters for the Authorization header or post 
 *  data (if application/x-www-form-urlencoded).
 * @return string Signature
 */
function sign_request( $method, $url, $params = array() ) {
    global $gConsumerSecret, $gTokenSecret;

    $parts = parse_url( $url );

    // We need to normalize the endpoint URL
    $scheme = isset( $parts['scheme'] ) ? $parts['scheme'] : 'http';
    $host = isset( $parts['host'] ) ? $parts['host'] : '';
    $port = isset( $parts['port'] ) ? $parts['port'] : ( $scheme == 'https' ? '443' : '80' );
    $path = isset( $parts['path'] ) ? $parts['path'] : '';
    if ( ( $scheme == 'https' && $port != '443' ) ||
        ( $scheme == 'http' && $port != '80' ) 
    ) {
        // Only include the port if it's not the default
        $host = "$host:$port";
    }

    // Also the parameters
    $pairs = array();
    parse_str( isset( $parts['query'] ) ? $parts['query'] : '', $query );
    $query += $params;
    unset( $query['oauth_signature'] );
    if ( $query ) {
        $query = array_combine(
            // rawurlencode follows RFC 3986 since PHP 5.3
            array_map( 'rawurlencode', array_keys( $query ) ),
            array_map( 'rawurlencode', array_values( $query ) )
        );
        ksort( $query, SORT_STRING );
        foreach ( $query as $k => $v ) {
            $pairs[] = "$k=$v";
        }
    }

    $toSign = rawurlencode( strtoupper( $method ) ) . '&' .
        rawurlencode( "$scheme://$host$path" ) . '&' .
        rawurlencode( join( '&', $pairs ) );
    $key = rawurlencode( $gConsumerSecret ) . '&' . rawurlencode( $gTokenSecret );
    return base64_encode( hash_hmac( 'sha1', $toSign, $key, true ) );
}

/**
 * Request authorization
 * @return void
 */
function doAuthorizationRedirect() {
    global $mwOAuthUrl, $mwOAuthAuthorizeUrl, $gUserAgent, $gConsumerKey, $errorCode;

    // First, we need to fetch a request token.
    // The request is signed with an empty token secret and no token key.
    // ----------------------------------------------
    // ----------------------------------------------
    $state = [];
    // login5.php?action=login&cat=RTT&depth=1&code=&type=lead
    
    foreach (['cat', 'depth', 'code', 'type'] as $key) {
        if ($_REQUEST[$key]) {
            $state[] = $key . '=' . $_REQUEST[$key];
        }
    };
    $state = implode('&', $state);
    // ----------------------------------------------
    // echo $state;
    // ----------------------------------------------
    $oauth_callback = 'https://mdwiki.toolforge.org/Translation_Dashboard/index.php' . '?' . $state ;
    // ----------------------------------------------
    // ----------------------------------------------
    // $gTokenSecret = '';
    $url = $mwOAuthUrl . '/initiate';
    $url .= strpos( $url, '?' ) ? '&' : '?';
    $url .= http_build_query( array(
        'format' => 'json',
        
        // OAuth information
        'oauth_callback' => $oauth_callback, // Must be "oob" or something prefixed by the configured callback URL
        'oauth_consumer_key' => $gConsumerKey,
        'oauth_version' => '1.0',
        'oauth_nonce' => md5( microtime() . mt_rand() ),
        'oauth_timestamp' => time(),

        // We're using secret key signatures here.
        'oauth_signature_method' => 'HMAC-SHA1',
    ) );
    $signature = sign_request( 'GET', $url );
    $url .= "&oauth_signature=" . urlencode( $signature );
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $url );
    //curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch, CURLOPT_USERAGENT, $gUserAgent );
    curl_setopt( $ch, CURLOPT_HEADER, 0 );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    $data = curl_exec( $ch );
    if ( !$data ) {
        // header( "HTTP/1.1 $errorCode Internal Server Error" );
        echo '- Curl error: ' . htmlspecialchars( curl_error( $ch ) );
        // throw new Exception ( '- Curl error: ' . htmlspecialchars( curl_error( $ch ) ) ) ;
        // exit(0);
    }
    curl_close( $ch );
    $token = json_decode( $data );
    if ( is_object( $token ) && isset( $token->error ) ) {
        // header( "HTTP/1.1 $errorCode Internal Server Error" );
        echo 'Error when retrieving token: ' . htmlspecialchars( $token->error ) . '<br>' . htmlspecialchars( $token->message );
        // exit(0);
    }
    if ( !is_object( $token ) || !isset( $token->key ) || !isset( $token->secret ) ) {
        // header( "HTTP/1.1 $errorCode Internal Server Error" );
        echo 'Invalid response from token request';
        // exit(0);
    }
    //---
    //echo var_dump($token);
    //---
    // Now we have the request token, we need to save it for later.
    session_start();
    $_SESSION['tokenKey'] = $token->key;
    $_SESSION['tokenSecret'] = $token->secret;
    session_write_close();

    // Then we send the user off to authorize
    $url = $mwOAuthAuthorizeUrl;
    $url .= strpos( $url, '?' ) ? '&' : '?';
    $url .= http_build_query( array(
        'oauth_token' => $token->key,
        'oauth_consumer_key' => $gConsumerKey,
    ) );
    //---
    // if ( $_REQUEST['type'] != 'test' ) {
    header( "Location: $url" );
    // };
    //---
    echo 'Please see <a href="' . htmlspecialchars( $url ) . '">' . htmlspecialchars( $url ) . '</a>';
}

/**
 * Handle a callback to fetch the access token
 * @return void
 */
function fetchAccessToken() {
    global $mwOAuthUrl, $gUserAgent, $gConsumerKey, $gTokenKey, $gTokenSecret, $errorCode;
    global $twoYears;
    
    $url = $mwOAuthUrl . '/token';
    $url .= strpos( $url, '?' ) ? '&' : '?';
    $url .= http_build_query( array(
        'format' => 'json',
        'oauth_verifier' => $_REQUEST['oauth_verifier'],

        // OAuth information
        'oauth_consumer_key' => $gConsumerKey,
        'oauth_token' => $gTokenKey,
        'oauth_version' => '1.0',
        'oauth_nonce' => md5( microtime() . mt_rand() ),
        'oauth_timestamp' => time(),

        // We're using secret key signatures here.
        'oauth_signature_method' => 'HMAC-SHA1',
    ) );
    $signature = sign_request( 'GET', $url );
    $url .= "&oauth_signature=" . urlencode( $signature );
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $url );
    //curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch, CURLOPT_USERAGENT, $gUserAgent );
    curl_setopt( $ch, CURLOPT_HEADER, 0 );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    $data = curl_exec( $ch );
    if ( !$data ) {
        header( "HTTP/1.1 $errorCode Internal Server Error" );
        echo '* Curl error: ' . htmlspecialchars( curl_error( $ch ) );
        exit(0);
    }
    curl_close( $ch );
    $token = json_decode( $data );
    if ( is_object( $token ) && isset( $token->error ) ) {
        header( "HTTP/1.1 $errorCode Internal Server Error" );
        //echo 'Error retrieving token: ' . htmlspecialchars( $token->error ) . '<br>' . htmlspecialchars( $token->message );
        exit(0);
    }
    if ( !is_object( $token ) || !isset( $token->key ) || !isset( $token->secret ) ) {
        header( "HTTP/1.1 $errorCode Internal Server Error" );
        echo 'Invalid response from token request';
        exit(0);
    }

    // Save the access token
    session_start();
    $gTokenKey = $token->key;
    $gTokenSecret = $token->secret;
    
    $_SESSION['tokenKey'] = $token->key;
    setcookie('tokenKey',$gTokenKey,$twoYears,'/','mdwiki.toolforge.org',true,true);
    
    $_SESSION['tokenSecret'] = $token->secret;
    setcookie('tokenSecret',$gTokenSecret,$twoYears,'/','mdwiki.toolforge.org',true,true);
    
    session_write_close();
}

/**
 * Request a JWT and verify it
 * @return void
 */
function doIdentify($gg) {
    global $mwOAuthUrl, $gUserAgent, $gConsumerKey, $gTokenKey, $gConsumerSecret, $errorCode;
    global $twoYears;
    global $username;

    $url = $mwOAuthUrl . '/identify';
    $headerArr = array(
        // OAuth information
        'oauth_consumer_key' => $gConsumerKey,
        'oauth_token' => $gTokenKey,
        'oauth_version' => '1.0',
        'oauth_nonce' => md5( microtime() . mt_rand() ),
        'oauth_timestamp' => time(),

        // We're using secret key signatures here.
        'oauth_signature_method' => 'HMAC-SHA1',
    );
    $signature = sign_request( 'GET', $url, $headerArr );
    $headerArr['oauth_signature'] = $signature;

    $header = array();
    foreach ( $headerArr as $k => $v ) {
        $header[] = rawurlencode( $k ) . '="' . rawurlencode( $v ) . '"';
    }
    $header = 'Authorization: OAuth ' . join( ', ', $header );

    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array( $header ) );
    //curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch, CURLOPT_USERAGENT, $gUserAgent );
    curl_setopt( $ch, CURLOPT_HEADER, 0 );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    $data = curl_exec( $ch );
    if ( !$data ) {
        header( "HTTP/1.1 $errorCode Internal Server Error" );
        echo '# Curl error: ' . htmlspecialchars( curl_error( $ch ) );
        exit(0);
    }
    $err = json_decode( $data );
    //---
    if ( is_object( $err ) && isset( $err->error ) && $err->error === 'mwoauthdatastore-access-token-not-found' ) {
        // We're not authorized!
        //echo "You haven't authorized this application yet! Go <a href='" . htmlspecialchars( $_SERVER['SCRIPT_NAME'] ) . "?action=login'>here</a> to do that.";
        //echo "<hr>";
        return;
    }

    // There are three fields in the response
    $fields = explode( '.', $data );
    if ( count( $fields ) !== 3 ) {
        header( "HTTP/1.1 $errorCode Internal Server Error" );
        echo 'Invalid identify response: ' . htmlspecialchars( $data );
        exit(0);
    }

    // Validate the header. MWOAuth always returns alg "HS256".
    $header = base64_decode( strtr( $fields[0], '-_', '+/' ), true );
    if ( $header !== false ) {
        $header = json_decode( $header );
    }
    if ( !is_object( $header ) || $header->typ !== 'JWT' || $header->alg !== 'HS256' ) {
        header( "HTTP/1.1 $errorCode Internal Server Error" );
        echo 'Invalid header in identify response: ' . htmlspecialchars( $data );
        exit(0);
    }

    // Verify the signature
    $sig = base64_decode( strtr( $fields[2], '-_', '+/' ), true );
    $check = hash_hmac( 'sha256', $fields[0] . '.' . $fields[1], $gConsumerSecret, true );
    if ( $sig !== $check ) {
        header( "HTTP/1.1 $errorCode Internal Server Error" );
        echo 'JWT signature validation failed: ' . htmlspecialchars( $data );
        echo '<pre>'; var_dump( base64_encode($sig), base64_encode($check) ); echo '</pre>';
        exit(0);
    }

    // Decode the payload
    $payload = base64_decode( strtr( $fields[1], '-_', '+/' ), true );
    if ( $payload !== false ) {
        $payload = json_decode( $payload );
    }
    if ( !is_object( $payload ) ) {
        header( "HTTP/1.1 $errorCode Internal Server Error" );
        echo 'Invalid payload in identify response: ' . htmlspecialchars( $data );
        exit(0);
    }
    //---
    
    //return $payload
    //$dd = var_export( $payload, 1 );
    $username = $payload->{'username'};
    //---
    
    setcookie('username',$username,$twoYears,'/','mdwiki.toolforge.org',true,true);
    //---
    if ( $gg != '' ) {
        echo 'JWT payload: <pre>' . htmlspecialchars( var_export( $payload, 1 ) ) . '</pre><br><hr>';
        }
    //---
}

// ******************** WEBPAGE ********************

function doApiQuery( $post, &$ch = null ) {
    global $apiUrl, $gUserAgent, $gConsumerKey, $errorCode;
    //---
    //temps:
    global $gTokenKey;
    //---

    $headerArr = array(
        // OAuth information
        'oauth_consumer_key' => $gConsumerKey,
        'oauth_token' => $gTokenKey,
        'oauth_version' => '1.0',
        'oauth_nonce' => md5( microtime() . mt_rand() ),
        'oauth_timestamp' => time(),

        // We're using secret key signatures here.
        'oauth_signature_method' => 'HMAC-SHA1',
    );
    $signature = sign_request( 'POST', $apiUrl, $post + $headerArr );
    $headerArr['oauth_signature'] = $signature;

    $header = array();
    foreach ( $headerArr as $k => $v ) {
        $header[] = rawurlencode( $k ) . '="' . rawurlencode( $v ) . '"';
    }
    $header = 'Authorization: OAuth ' . join( ', ', $header );

    if ( !$ch ) {
        $ch = curl_init();
    }
    curl_setopt( $ch, CURLOPT_POST, true );
    curl_setopt( $ch, CURLOPT_URL, $apiUrl );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $post ) );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array( $header ) );
    //curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch, CURLOPT_USERAGENT, $gUserAgent );
    curl_setopt( $ch, CURLOPT_HEADER, 0 );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    $data = curl_exec( $ch );
    if ( !$data ) {
        header( "HTTP/1.1 $errorCode Internal Server Error" );
        echo 'Curl error: ' . htmlspecialchars( curl_error( $ch ) );
        exit(0);
    }
    $ret = json_decode( $data, true );
    if ( $ret === null ) {
        header( "HTTP/1.1 $errorCode Internal Server Error" );
        echo 'Unparsable API response: <pre>' . htmlspecialchars( $data ) . '</pre>';
        exit(0);
    }
    return $ret;
}

if ($_REQUEST['test'] != '' ) echo "<br>load " . str_replace ( __dir__ , '' , __file__ ) . " true.";
//}
//login()
?>