<?php

namespace Translate\EnAPI;

/*
Usage:
use function Translate\EnAPI\do_en_edit;
*/


include_once __DIR__ . '/../actions/access_helps.php';
include_once __DIR__ . '/../auth/send_edit.php';

use function OAuth\SendEdit\auth_do_edit;
use function Actions\AccessHelps\get_access_from_db;
use function Actions\Functions\test_print;

function do_en_edit($title, $text, $summary)
{
	// ---
	test_print("________________");
	// ---
	$user = "Mr. Ibrahem";
	// ---
	// if global_username is MdWikiBot then use it
	if (global_username == 'MdWikiBot') {
		$user = 'MdWikiBot';
	}
	$access = get_access_from_db($user);
	// ---
	if ($access == null) {
		$result = json_encode(['error' => 'no access', 'username' => $user]);
		test_print("do_en_edit: $result");
		return $result;
	};
	// ---
	$access_key = $access['access_key'];
	$access_secret = $access['access_secret'];
	// ---
    $editit = auth_do_edit($title, $text, $summary, "simple", $access_key, $access_secret);
	//---
	return $editit;
}
