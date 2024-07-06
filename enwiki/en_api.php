<?php

/*
    edit.php

    MediaWiki API Demos
    Demo of `Edit` module: POST request to edit a page
    MIT license
*/

include_once(__DIR__ . '/user_account_new.php');
$lgname_enwiki = $lgname_enwiki;
$lgpass_enwiki = $lgpass_enwiki;

$endPoint = "https://en.wikipedia.org/w/api.php";

// Step 1: GET request to fetch login token
function getLoginToken()
{
	global $endPoint;

	$params1 = [
		"action" => "query",
		"meta" => "tokens",
		"type" => "login",
		"format" => "json"
	];

	$url = $endPoint . "?" . http_build_query($params1);

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
	curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");

	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);

	$output = curl_exec($ch);
	curl_close($ch);
	// ---
	// echo "<pre>";
	// echo htmlentities(var_export($output, true));
	// echo "</pre><br>";
	//---
	$result = json_decode($output, true);
	return $result["query"]["tokens"]["logintoken"];
}

// Step 2: POST request to log in. Use of main account for login is not
// supported. Obtain credentials via Special:BotPasswords
// (https://www.mediawiki.org/wiki/Special:BotPasswords) for lgname & lgpassword
function loginRequest($logintoken)
{
	global $endPoint, $lgname_enwiki, $lgpass_enwiki;

	$params2 = [
		"action" => "login",
		"lgname" => $lgname_enwiki,
		"lgpassword" => $lgpass_enwiki,
		"lgtoken" => $logintoken,
		"format" => "json"
	];

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $endPoint);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params2));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
	curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");

	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);

	$output = curl_exec($ch);
	curl_close($ch);
}

// Step 3: GET request to fetch CSRF token
function getCSRFToken()
{
	global $endPoint;

	$params3 = [
		"action" => "query",
		"meta" => "tokens",
		"format" => "json"
	];

	$url = $endPoint . "?" . http_build_query($params3);

	$ch = curl_init($url);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
	curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");

	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);

	$output = curl_exec($ch);
	curl_close($ch);

	$result = json_decode($output, true);
	return $result["query"]["tokens"]["csrftoken"];
}

// Step 4: POST request to edit a page
function send_params($params4)
{
	global $endPoint;

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $endPoint);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params4));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
	curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");

	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);

	$output = curl_exec($ch);
	curl_close($ch);

	return $output;
}

function do_edit($title, $text, $summary)
{

	$login_Token = getLoginToken(); // Step 1
	loginRequest($login_Token); // Step 2
	$csrf_Token = getCSRFToken(); // Step 3

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
