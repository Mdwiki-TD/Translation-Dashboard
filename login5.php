<?php
if (isset($_REQUEST['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};
//---
include_once 'actions/mdwiki_sql.php';
//---
$mwOAuthAuthorizeUrl = 'https://mdwiki.org/wiki/Special:OAuth/authorize';
$mwOAuthUrl = 'https://mdwiki.org/w/index.php?title=Special:OAuth';
$apiUrl = 'https://mdwiki.org/w/api.php';
//---
$twoYears = time() + 60 * 60 * 24 * 365 * 2;
$errorCode = 200;
$SCRIPT_NAME = htmlspecialchars($_SERVER['SCRIPT_NAME']);
//---
session_name('OAuthHelloWorld');
$params = session_get_cookie_params();
session_set_cookie_params([
    'lifetime' => $params['lifetime'],
    'path' => dirname($_SERVER['SCRIPT_NAME']),
    'secure' => true,
    'httponly' => true
]);
//---
// get the root path from __file__ , split before public_html
// split the file path on the public_html directory
$pathParts = explode('public_html', __file__);

// the root path is the first part of the split file path
$ROOT_PATH = $pathParts[0];
//---
$inifile = $ROOT_PATH . '/confs/OAuthConfig.ini';
//---
$ini = parse_ini_file($inifile);
//---
if ($ini === false) {
    header("HTTP/1.1 $errorCode Internal Server Error");
    echo "The ini file:($inifile) could not be read";
    // exit(0);
}
if (
    !isset($ini['agent']) ||
    !isset($ini['consumerKey']) ||
    !isset($ini['consumerSecret'])
) {
    header("HTTP/1.1 $errorCode Internal Server Error");
    echo 'Required configuration directives not found in ini file';
    exit(0);
}
$gUserAgent      = $ini['agent'];
$gConsumerKey    = $ini['consumerKey'];
$gConsumerSecret = $ini['consumerSecret'];
$sqlpass = $ini['sqlpass'];
// Load the user token (request or access) from the session
//---
$server_name = 'mdwiki.toolforge.org';
$server_name = $_SERVER['SERVER_NAME'];
//---
$username = '';
if ($_SERVER['SERVER_NAME'] == 'localhost') {
    $fa = $_GET['test1'] ?? '';
    if ($fa == 'xx') {
        $username = 'Mr. Ibrahem';
        setcookie('username', $username, time() + $twoYears, '/', $server_name, true, true);
    };
};
//---
if (isset($_COOKIE['username'])) $username = $_COOKIE['username'];
//---
$gTokenKey = '';
$gTokenSecret = '';
//---
session_start();
//---
if (isset($_SESSION['tokenKey'])) {
    $gTokenKey = $_SESSION['tokenKey'];
    $gTokenSecret = $_SESSION['tokenSecret'];
} elseif (isset($_COOKIE['tokenKey'])) {
    $gTokenKey    = $_COOKIE['tokenKey'];
    $gTokenSecret = $_COOKIE['tokenSecret'];
};
//---
session_write_close();
//---
// Fetch the access token if this is the callback from requesting authorization
// we get it after login
// if ( isset($_REQUEST['oauth_verifier']) && isset($_REQUEST['oauth_token']) ) {
if (isset($_REQUEST['oauth_verifier'])) {
    fetchAccessToken();
    //---
    if ($gTokenSecret != '' and $gTokenKey != '') doIdentify('');
    //---
};
//---
function log_new_user($username)
{
    sql_add_user($username, '', '', '', '');
}
//---
function sign_request($method, $url, $params = array())
{
    global $gConsumerSecret, $gTokenSecret;

    $parts = parse_url($url);

    // We need to normalize the endpoint URL
    $scheme =  $parts['scheme']  ?? 'http';
    $host =  $parts['host']  ?? '';
    $port =  $parts['port']  ?? ($scheme == 'https' ? '443' : '80');
    $path =  $parts['path']  ?? '';
    if (($scheme == 'https' && $port != '443') ||
        ($scheme == 'http' && $port != '80')
    ) {
        // Only include the port if it's not the default
        $host = "$host:$port";
    }

    // Also the parameters
    $pairs = array();
    parse_str($parts['query']  ?? '', $query);
    $query += $params;
    unset($query['oauth_signature']);
    if ($query) {
        $query = array_combine(
            // rawurlencode follows RFC 3986 since PHP 5.3
            array_map('rawurlencode', array_keys($query)),
            array_map('rawurlencode', array_values($query))
        );
        ksort($query, SORT_STRING);
        foreach ($query as $k => $v) {
            $pairs[] = "$k=$v";
        }
    }

    $toSign = rawurlencode(strtoupper($method)) . '&' .
        rawurlencode("$scheme://$host$path") . '&' .
        rawurlencode(join('&', $pairs));
    $key = rawurlencode($gConsumerSecret) . '&' . rawurlencode($gTokenSecret);
    return base64_encode(hash_hmac('sha1', $toSign, $key, true));
}

/**
 * Request authorization
 * @return void
 */

function doAuthorizationRedirect()
{
    global $mwOAuthUrl, $mwOAuthAuthorizeUrl, $gUserAgent, $gConsumerKey, $errorCode;

    // First, we need to fetch a request token.
    // The request is signed with an empty token secret and no token key.
    //---
    $state = array();
    // login5.php?action=login&cat=RTT&depth=1&code=&type=lead

    foreach (['cat', 'code', 'type', 'test'] as $key) {
        $da = $_REQUEST[$key] ?? '';
        if ($da != '') $state[$key] = $da;
    };
    // $state = implode('&', $state);
    $state = http_build_query($state);
    //---
    // echo $state;
    //---
    $oauth_call = 'https://mdwiki.toolforge.org/Translation_Dashboard/index.php' . '?' . $state;
    //---
    // $gTokenSecret = '';
    $url_ar = array(
        'format' => 'json',
        // OAuth information
        'oauth_callback' => $oauth_call, // Must be "oob" or something prefixed by the configured callback URL
        'oauth_consumer_key' => $gConsumerKey,
        'oauth_version' => '1.0',
        'oauth_nonce' => md5(microtime() . mt_rand()),
        'oauth_timestamp' => time(),
        // We're using secret key signatures here.
        'oauth_signature_method' => 'HMAC-SHA1',
    );
    //---
    $url = $mwOAuthUrl . '/initiate' . '&' . http_build_query($url_ar);
    //---
    if (isset($_REQUEST['test'])) echo "<br>$url<br>";
    //---
    $signature = sign_request('GET', $url);
    //---
    $url .= "&oauth_signature=" . urlencode($signature);
    //---
    if (isset($_REQUEST['test'])) echo "<br>signature: $signature<br>";
    //---
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    //curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt($ch, CURLOPT_USERAGENT, $gUserAgent);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //---
    $data = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if (!$data || $httpcode != 200) {
        header("HTTP/1.1 $errorCode Internal Server Error");
        echo '- Curl error: ' . htmlspecialchars(curl_error($ch));
        echo '- HTTP status: ' . $httpcode;
        // throw new Exception ( '- Curl error: ' . htmlspecialchars( curl_error( $ch ) ) ) ;
        exit(0);
    }
    curl_close($ch);
    //---
    $token = json_decode($data);
    //---
    if (is_object($token) && isset($token->error)) {
        echo 'Error when retrieving token: ' . htmlspecialchars($token->error) . '<br>' . htmlspecialchars($token->message);
        if (isset($_REQUEST['test'])) {
            header("HTTP/1.1 $errorCode Internal Server Error");
            exit(0);
        };
    }
    if (!is_object($token) || !isset($token->key) || !isset($token->secret)) {
        echo 'doAuthorizationRedirect: Invalid response from token request';
        if (isset($_REQUEST['test'])) {
            header("HTTP/1.1 $errorCode Internal Server Error");
            exit(0);
        };
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
    $url .= strpos($url, '?') ? '&' : '?';
    $url .= http_build_query(array(
        'oauth_token' => $token->key,
        'oauth_consumer_key' => $gConsumerKey,
    ));
    //---
    // if ( $_REQUEST['type'] != 'test' ) {
    header("Location: $url");
    // };
    //---
    echo 'Please see <a href="' . htmlspecialchars($url) . '">' . htmlspecialchars($url) . '</a>';
}

/**
 * Handle a callback to fetch the access token
 * @return void
 */
function fetchAccessToken()
{
    global $mwOAuthUrl, $gUserAgent, $gConsumerKey, $gTokenKey, $gTokenSecret, $errorCode;
    global $twoYears;

    $url = $mwOAuthUrl . '/token';
    $url .= strpos($url, '?') ? '&' : '?';
    $url .= http_build_query(array(
        'format' => 'json',
        'oauth_verifier' => $_REQUEST['oauth_verifier'],

        // OAuth information
        'oauth_consumer_key' => $gConsumerKey,
        'oauth_token' => $gTokenKey,
        'oauth_version' => '1.0',
        'oauth_nonce' => md5(microtime() . mt_rand()),
        'oauth_timestamp' => time(),

        // We're using secret key signatures here.
        'oauth_signature_method' => 'HMAC-SHA1',
    ));
    $signature = sign_request('GET', $url);
    $url .= "&oauth_signature=" . urlencode($signature);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    //curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt($ch, CURLOPT_USERAGENT, $gUserAgent);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //---
    $data = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if (!$data || $httpcode != 200) {
        header("HTTP/1.1 $errorCode Internal Server Error");
        echo '- Curl error: ' . htmlspecialchars(curl_error($ch));
        echo '- HTTP status: ' . $httpcode;
        // throw new Exception ( '- Curl error: ' . htmlspecialchars( curl_error( $ch ) ) ) ;
        exit(0);
    }
    //---
    curl_close($ch);
    $token = json_decode($data);
    if (is_object($token) && isset($token->error)) {
        header("HTTP/1.1 $errorCode Internal Server Error");
        //echo 'Error retrieving token: ' . htmlspecialchars( $token->error ) . '<br>' . htmlspecialchars( $token->message );
        exit(0);
    }
    if (!is_object($token) || !isset($token->key) || !isset($token->secret)) {
        header("HTTP/1.1 $errorCode Internal Server Error");
        echo 'fetchAccessToken: Invalid response from token request';
        exit(0);
    }

    // Save the access token
    session_start();
    $gTokenKey = $token->key;
    $gTokenSecret = $token->secret;

    $_SESSION['tokenKey'] = $token->key;
    // setcookie('tokenKey',$gTokenKey,time()+$twoYears,'/',$server_name,true,true);

    $_SESSION['tokenSecret'] = $token->secret;
    // setcookie('tokenSecret',$gTokenSecret,time()+$twoYears,'/',$server_name,true,true);

    session_write_close();
}

/**
 * Request a JWT and verify it
 * @return void
 */
function doIdentify($gg)
{
    global $mwOAuthUrl, $gUserAgent, $gConsumerKey, $gTokenKey, $gConsumerSecret, $errorCode;
    global $twoYears;
    global $username, $server_name;

    $url = $mwOAuthUrl . '/identify';
    $headerArr = array(
        // OAuth information
        'oauth_consumer_key' => $gConsumerKey,
        'oauth_token' => $gTokenKey,
        'oauth_version' => '1.0',
        'oauth_nonce' => md5(microtime() . mt_rand()),
        'oauth_timestamp' => time(),

        // We're using secret key signatures here.
        'oauth_signature_method' => 'HMAC-SHA1',
    );
    $signature = sign_request('GET', $url, $headerArr);
    $headerArr['oauth_signature'] = $signature;

    $header = array();
    foreach ($headerArr as $k => $v) {
        $header[] = rawurlencode($k) . '="' . rawurlencode($v) . '"';
    }
    $header = 'Authorization: OAuth ' . join(', ', $header);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
    //curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt($ch, CURLOPT_USERAGENT, $gUserAgent);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //---
    $data = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if (!$data || $httpcode != 200) {
        header("HTTP/1.1 $errorCode Internal Server Error");
        echo '- Curl error: ' . htmlspecialchars(curl_error($ch));
        echo '- HTTP status: ' . $httpcode;
        // throw new Exception ( '- Curl error: ' . htmlspecialchars( curl_error( $ch ) ) ) ;
        exit(0);
    }
    //---
    $err = json_decode($data);
    //---
    if (is_object($err) && isset($err->error) && $err->error === 'mwoauthdatastore-access-token-not-found') {
        // We're not authorized!
        //echo "You haven't authorized this application yet! Go <a href='" . htmlspecialchars( $_SERVER['SCRIPT_NAME'] ) . "?action=login'>here</a> to do that.";
        //echo "<hr>";
        return;
    }

    // There are three fields in the response
    $fields = explode('.', $data);
    if (count($fields) !== 3) {
        header("HTTP/1.1 $errorCode Internal Server Error");
        echo 'Invalid identify response: ' . htmlspecialchars($data);
        exit(0);
    }

    // Validate the header. MWOAuth always returns alg "HS256".
    $header = base64_decode(strtr($fields[0], '-_', '+/'), true);
    if ($header !== false) {
        $header = json_decode($header);
    }
    if (!is_object($header) || $header->typ !== 'JWT' || $header->alg !== 'HS256') {
        header("HTTP/1.1 $errorCode Internal Server Error");
        echo 'Invalid header in identify response: ' . htmlspecialchars($data);
        exit(0);
    }

    // Verify the signature
    $sig = base64_decode(strtr($fields[2], '-_', '+/'), true);
    $check = hash_hmac('sha256', $fields[0] . '.' . $fields[1], $gConsumerSecret, true);
    if ($sig !== $check) {
        header("HTTP/1.1 $errorCode Internal Server Error");
        echo 'JWT signature validation failed: ' . htmlspecialchars($data);
        echo '<pre>';
        var_dump(base64_encode($sig), base64_encode($check));
        echo '</pre>';
        exit(0);
    }

    // Decode the payload
    $payload = base64_decode(strtr($fields[1], '-_', '+/'), true);
    if ($payload !== false) {
        $payload = json_decode($payload);
    }
    if (!is_object($payload)) {
        header("HTTP/1.1 $errorCode Internal Server Error");
        echo 'Invalid payload in identify response: ' . htmlspecialchars($data);
        exit(0);
    }
    //---
    //return $payload
    //$dd = var_export( $payload, 1 );
    $username = $payload->{'username'};
    //---
    setcookie('username', $username, time() + $twoYears, '/', $server_name, true, true);
    //---
    if ($username == 'Mr. Ibrahem') {
        # dump $payload
        echo var_export($payload, 1);
    }
    //---
    log_new_user($username);
    //---
    if ($gg != '') {
        echo 'JWT payload: <pre>' . htmlspecialchars(var_export($payload, 1)) . '</pre><br><hr>';
    }
    //---
}

//--- WEBPAGE ********************

function doApiQuery($post, $ch = null, $addtoken = false)
{
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
        'oauth_nonce' => md5(microtime() . mt_rand()),
        'oauth_timestamp' => time(),

        // We're using secret key signatures here.
        'oauth_signature_method' => 'HMAC-SHA1',
    );
    $signature = sign_request('POST', $apiUrl, $post + $headerArr);
    $headerArr['oauth_signature'] = $signature;

    $header = array();
    foreach ($headerArr as $k => $v) {
        $header[] = rawurlencode($k) . '="' . rawurlencode($v) . '"';
    }
    $header = 'Authorization: OAuth ' . join(', ', $header);

    if (!$ch) {
        $ch = curl_init();
    }
    //---
    if ($addtoken) {
        $post['token'] = get_csrftoken();
    }
    //---
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
    //curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt($ch, CURLOPT_USERAGENT, $gUserAgent);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //---
    $data = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if (!$data || $httpcode != 200) {
        header("HTTP/1.1 $errorCode Internal Server Error");
        echo '- Curl error: ' . htmlspecialchars(curl_error($ch));
        echo '- HTTP status: ' . $httpcode;
        // throw new Exception ( '- Curl error: ' . htmlspecialchars( curl_error( $ch ) ) ) ;
        exit(0);
    }
    //---
    $ret = json_decode($data, true);
    //---
    if ($ret === null) {
        header("HTTP/1.1 $errorCode Internal Server Error");
        echo 'Unparsable API response: <pre>' . htmlspecialchars($data) . '</pre>';
        exit(0);
    };
    //---
    return $ret;
}

function get_csrftoken()
{
    global $errorCode;

    $ch = null;

    // First fetch the username
    $res = doApiQuery(array(
        'format' => 'json',
        'action' => 'query',
        'meta' => 'userinfo',
    ), $ch);

    if (isset($res->error->code) && $res->error->code === 'mwoauth-invalid-authorization') {
        // We're not authorized!
        echo 'You haven\'t authorized this application yet! Go <a href="' . htmlspecialchars($_SERVER['SCRIPT_NAME']) . '?action=authorize">here</a> to do that.';
        echo '<hr>';
        return;
    }

    // { "batchcomplete": "", "query": { "userinfo": { "id": 13, "name": "Mr. Ibrahem" } }
    if (!isset($res->query->userinfo)) {
        header("HTTP/1.1 $errorCode Internal Server Error");
        echo 'Bad API response: <pre>' . htmlspecialchars(json_encode($res)) . '</pre>';
        exit(0);
    }
    if (isset($res->query->userinfo->anon)) {
        header("HTTP/1.1 $errorCode Internal Server Error");
        echo 'Not logged in. (How did that happen?)';
        exit(0);
    }
    // Next fetch the edit token
    $res = doApiQuery(array(
        'format' => 'json',
        'action' => 'query',
        'meta' => 'tokens',
        'type' => '*',
    ), $ch);
    // "createaccounttoken", "csrftoken", "logintoken", "patroltoken", "rollbacktoken", "userrightstoken", "watchtoken"
    if (!isset($res->query->tokens->csrftoken)) {
        header("HTTP/1.1 $errorCode Internal Server Error");
        echo 'Bad API response: <pre>' . htmlspecialchars(json_encode($res)) . '</pre>';
        exit(0);
    }
    $token = $res->query->tokens->csrftoken;

    // add the token to $data
    return $token;
}

function doEdit($data)
{
    $ch = null;
    // add the token to $data
    // Now perform the edit
    $res = doApiQuery($data, $ch, true);
    return $res;
    // echo 'API edit result: <pre>' . htmlspecialchars( var_export( $res, 1 ) ) . '</pre>';
    // echo '<hr>';
}

// Take any requested action
switch (isset($_REQUEST['action']) ? $_REQUEST['action'] : '') {
    case 'login':
        if ($_SERVER['SERVER_NAME'] == 'localhost') {
            $fa = $_GET['test1'] ?? '';
            if ($fa == '') {
                $username = 'Mr. Ibrahem';
                log_new_user($username);
                setcookie('username', $username, time() + $twoYears, '/', $server_name, true, true);
                header("Location: " . $_SERVER['HTTP_REFERER']);
            };
        };
        doAuthorizationRedirect();
        return;

    case 'logout':
        // session_unset();
        //---
        session_start();
        session_destroy();
        //---
        // unset cookies
        setcookie('username', '', time() - $twoYears, '/', $server_name, true, true);
        setcookie('OAuthHelloWorld', '', time() - $twoYears, '/', $server_name, true, true);
        if (isset($_SERVER['HTTP_COOKIE'])) {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach ($cookies as $cookie) {
                $parts = explode('=', $cookie);
                $name = trim($parts[0]);
                setcookie($name, '', time() - $twoYears, '/', $server_name, true, true);
            };
        };
        // session_start();

        unset($_COOKIE['username']);
        unset($_SESSION["tokenKey"]);
        unset($_SESSION["tokenSecret"]);
        unset($username);
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
        break;

    case 'identify':
        $ma = doIdentify('n');
        break;
}
