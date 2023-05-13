<?php
//---
function qu_str($string) {
	$str2 = "'$string'";
	//---
	if (strpos($string, "'") !== false)	$str2 = '"' . $string . "'";
	//---
	return $str2;
};
//---
function add_to_db($title, $type, $cat, $lang, $user, $target, $pupdate) {
    //---
	global $Words_table, $All_Words_table;
    //---
	$user    = trim($user);
	$lang    = trim($lang);
	$target  = trim($target);
	$pupdate = trim($pupdate);
	$title   = trim($title);
    //---
    $user 		= rawurldecode($user);
    $cat		= rawurldecode($cat);
    $title2		= qu_str($title);
    $target2	= qu_str($target);
    //---
    $word = $Words_table[$title] ?? 0; 
    if ($type == 'all') $word = $All_Words_table[$title] ?? 0;
    //---
	// date now format like 2023-01-01
	$add_date = date('Y-m-d');
	//---
	$qua_23 = <<<SQL
	UPDATE pages 
		SET target = $target2, pupdate = '$pupdate', word = '$word'
	WHERE user = '$user' AND title = $title2 AND lang = '$lang' and target = '';

	INSERT INTO pages (title, word, translate_type, cat, lang, date, user, pupdate, target, add_date)
		SELECT '$title', '$word', '$type', '$cat', '$lang', now(), '$user', '$pupdate', $target2, '$add_date'
	WHERE NOT EXISTS (SELECT 1 FROM pages WHERE title = $title2 AND lang = '$lang' AND user = '$user' );

	SQL;
    //---
	if (isset($_REQUEST['test'])) echo $qua_23;
    //---
    execute_query($qua_23);
    //---
};
//---
if (isset($_POST['mdtitle'])) {
	for($i = 0; $i < count($_POST['mdtitle']); $i++ ) {
		//---
		$mdtitle	= $_REQUEST['mdtitle'][$i] ?? '';
		$cat		= $_REQUEST['cat'][$i] ?? '';
		$type		= $_REQUEST['type'][$i] ?? '';
		$user		= $_REQUEST['user'][$i] ?? '';
		$lang		= $_REQUEST['lang'][$i] ?? '';
		$target		= $_REQUEST['target'][$i] ?? '';
		$pupdate	= $_REQUEST['pupdate'][$i] ?? '';
		//---
		if ($mdtitle != '' && $lang != '' && $user != '' && $target != '') {
			//---
			add_to_db($mdtitle, $type, $cat, $lang, $user, $target, $pupdate);
			//---
		};
	};
};
//---
?>