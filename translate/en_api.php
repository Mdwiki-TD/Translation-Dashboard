<?php

namespace Translate\EnAPI;

/*
Usage:

use function Translate\EnAPI\getLoginToken;
use function Translate\EnAPI\loginRequest;
use function Translate\EnAPI\getCSRFToken;
use function Translate\EnAPI\send_params;
use function Translate\EnAPI\do_edit;
use function Translate\EnAPI\Find_pages_exists_or_not;

*/

/*
    edit.php

    MediaWiki API Demos
    Demo of `Edit` module: POST request to edit a page
    MIT license
*/


include_once(__DIR__ . '/../infos/user_account_new.php');

use function Actions\Functions\test_print;

$my_username = $my_username;
$lgpass_enwiki = $lgpass_enwiki;
$usr_agent = $user_agent;

$endPoint = "https://simple.wikipedia.org/w/api.php";

function post_url_params_enwiki(string $endPoint, array $params = []): string
{
    $usr_agent = "WikiProjectMed Translation Dashboard/1.0 (https://mdwiki.toolforge.org/; tools.mdwiki@toolforge.org)";

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $endPoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
    // curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
    curl_setopt($ch, CURLOPT_USERAGENT, $usr_agent);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);

    $output = curl_exec($ch);
    $url = "{$endPoint}?" . http_build_query($params);
    // ---
    // remove "&format=json" from $url then make it link <a href="$url2">
    $url2 = str_replace('&format=json', '', $url);
    $url2 = "<a target='_blank' href='$url2'>$url2</a>";
    //---
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //---
    if ($http_code !== 200) {
        test_print('Error: API request failed with status code ' . $http_code);
    }
    //---
    test_print("post_url_params_enwiki:(http_code: $http_code) $url2");
    // ---
    if ($output === FALSE) {
        test_print("cURL Error: " . curl_error($ch));
    }

    if (curl_errno($ch)) {
        test_print('Error:' . curl_error($ch));
    }


    curl_close($ch);
    return $output;
}

function send_params($params, $funcs)
{
	global $endPoint;
	// ---
	$output = post_url_params_enwiki($endPoint, $params);
	// ---
	test_print("$funcs: " . json_encode($output));
	//---
	return $output;
}

// Step 1: GET request to fetch login token
function getLoginToken()
{
	$params1 = [
		"action" => "query",
		"meta" => "tokens",
		"type" => "login",
		"format" => "json"
	];
	// ---
	$output = send_params($params1, "getLoginToken");
	// ---
	$result = json_decode($output, true);
	if (!is_array($result)) {
		$result = array();
	}
	return $result["query"]["tokens"]["logintoken"] ?? "";
}

// Step 2: POST request to log in. Use of main account for login is not
// supported. Obtain credentials via Special:BotPasswords
// (https://www.mediawiki.org/wiki/Special:BotPasswords) for lgname & lgpassword
function loginRequest($logintoken)
{
	global $my_username, $lgpass_enwiki;

	$params2 = [
		"action" => "login",
		"lgname" => $my_username,
		"lgpassword" => $lgpass_enwiki,
		"lgtoken" => $logintoken,
		"format" => "json"
	];
	// ---
	$output = send_params($params2, "loginRequest");
	// ---
}

// Step 3: GET request to fetch CSRF token
function getCSRFToken()
{
	$params3 = [
		"action" => "query",
		"meta" => "tokens",
		"format" => "json"
	];
	// ---
	$output = send_params($params3, "getCSRFToken");
	// ---
	$result = json_decode($output, true);
	//---
	if (!is_array($result)) {
		$result = array();
	}
	return $result["query"]["tokens"]["csrftoken"] ?? "";
}


function do_edit($title, $text, $summary)
{

	$login_Token = getLoginToken(); // Step 1
	// ---
	if ($login_Token == "") {
		echo "<br>Login Token not found<br>";
		return false;
	}
	// ---
	test_print("Login Token: " . $login_Token);
	// ---
	loginRequest($login_Token); // Step 2
	// ---
	$csrf_Token = getCSRFToken(); // Step 3
	// ---
	if ($csrf_Token == "") {
		echo "<br>CSRF Token not found<br>";
		return false;
	}
	// ---
	$params4 = [
		"action" => "edit",
		"title" => $title,
		"text" => $text,
		"token" => $csrf_Token,
		"summary" => $summary,
		"format" => "json"
	];

	$result = send_params($params4);

	$result = json_decode($result, true);
	//---
	// echo "<pre>"; print_r($result); echo "</pre>";
	//---
	return $result;
}

function Find_pages_exists_or_not($title)
{
	// {"action": "query", "titles": title, "rvslots": "*"}
	$params = [
		"action" => "query",
		"titles" => $title,
		'format' => 'json',
		"formatversion" => 2
	];

	$result = send_params($params);

	$result = json_decode($result, true);
	$result = $result['query']['pages'] ?? [];
	// ---
	test_print(json_encode($result));
	// ---
	if (count($result) > 0) {
		$page = $result[0];
		$misssing = $page['missing'] ?? '';
		$pageid = $page['pageid'] ?? '';
		// ---
		if ($misssing == '' || $pageid != '') {
			return true;
		}
	}
	return false;
}
