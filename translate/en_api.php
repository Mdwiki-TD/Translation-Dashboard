<?php

namespace Translate\EnAPI;

/*
Usage:
use function Translate\EnAPI\do_edit;
*/


include_once __DIR__ . '/../publish/helps.php';
include_once __DIR__ . '/../publish/send_edit.php';

use function Publish\Edit\send_edit;
use function Publish\Helps\get_access_from_db;
use function Actions\Functions\test_print;

function do_edit($title, $text, $summary)
{
	// ---
	test_print("________________");
	// ---
	$user = "Mr. Ibrahem";
	// ---
	$access = get_access_from_db($user);
	// ---
	if ($access == null) {
		$editit = ['error' => 'no access', 'username' => $user];
		// exit(1);
	} else {
		$access_key = $access['access_key'];
		$access_secret = $access['access_secret'];
		// $text = get_medwiki_text($title);
		$editit = send_edit($title, $text, $summary, "simple", $access_key, $access_secret);
	}
	//---
	return $editit;
}
