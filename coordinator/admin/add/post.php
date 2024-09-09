<?php
//---

use function Actions\MdwikiSql\execute_query;

function insert_to_pages($t)
{
	//---
	$query1 = <<<SQL
        UPDATE pages
            SET target = ?, pupdate = ?, word = ?
        WHERE user = ? AND title = ? AND lang = ? and target = '';
    SQL;
	//---
	$params1 = [$t['target'], $t['pupdate'], $t['word'], $t['user'], $t['title'], $t['lang']];
	//---
	$result1 = execute_query($query1, $params1);
	//---
	$query2 = <<<SQL
        INSERT INTO pages (title, word, translate_type, cat, lang, date, user, pupdate, target, add_date)
            SELECT ?, ?, ?, ?, ?, now(), ?, ?, ?, now()
        WHERE NOT EXISTS (SELECT 1 FROM pages WHERE title = ? AND lang = ? AND user = ? );
    SQL;
	//---
	$params2 = [$t['title'], $t['word'], $t['type'], $t['cat'], $t['lang'], $t['user'], $t['pupdate'], $t['target'], $t['title'], $t['lang'], $t['user']];
	//---
	if (isset($_REQUEST['test'])) echo "$query1<br/>$query2";
	//---
	$result2 = execute_query($query2, $params2);
	//---
	return $result2;
}

function add_to_db($title, $type, $cat, $lang, $user, $target, $pupdate)
{
	//---
	global $Words_table, $All_Words_table;
	//---
	$word = $Words_table[$title] ?? 0;
	if ($type == 'all') $word = $All_Words_table[$title] ?? 0;
	//---
	// add them all to array
	$t = [
		'user'		=> trim($user),
		'lang'		=> trim($lang),
		'title'		=> trim($title),
		'target'	=> trim($target),
		'pupdate'	=> trim($pupdate),
		'cat'		=> trim($cat),
		'type'		=> trim($type),
		'word'		=> $word
	];
	//---
	insert_to_pages($t);
	//---
};
//---
if (isset($_POST['mdtitle'])) {
	for ($i = 0; $i < count($_POST['mdtitle']); $i++) {
		//---
		$mdtitle	= $_REQUEST['mdtitle'][$i] ?? '';
		$cat		= rawurldecode($_REQUEST['cat'][$i]) ?? '';
		$type		= $_REQUEST['type'][$i] ?? '';
		$user		= rawurldecode($_REQUEST['user'][$i]) ?? '';
		$lang		= $_REQUEST['lang'][$i] ?? '';
		$target		= $_REQUEST['target'][$i] ?? '';
		$pupdate	= $_REQUEST['pupdate'][$i] ?? '';
		//---
		if (!empty($mdtitle) && !empty($lang) && !empty($user) && !empty($target)) {
			//---
			add_to_db($mdtitle, $type, $cat, $lang, $user, $target, $pupdate);
			//---
		};
	};
};
//---
