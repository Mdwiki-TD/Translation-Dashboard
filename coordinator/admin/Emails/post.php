<?php
//---
use function Actions\MdwikiSql\sql_add_user;
use function Actions\MdwikiSql\execute_query;
//---
$new_q = "INSERT INTO users (username, email, wiki, user_group, reg_date) SELECT DISTINCT user, '', '', '', now() from pages
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = user)";
//---
if (isset($_POST['del'])) {
	var_export($_POST['del']);
	for($i = 0; $i < count($_POST['del']); $i++ ) {
		$del	= $_POST['del'][$i];
		//---
		if (!empty($del)) {
			$qu = "DELETE FROM users WHERE user_id = ?";
			execute_query($qu, $params=[$del]);
		};
	};
};
//---
if (isset($_POST['username'])) {
	for($i = 0; $i < count($_POST['username']); $i++ ){
		//---
		$user_name 	= $_POST['username'][$i];
		$email 	= $_POST['email'][$i];
		$ido 	= $_POST['id'][$i];
		$ido 	= (isset($ido)) ? $ido : '';
		$wiki 	= $_POST['wiki'][$i];
		$project 	= $_POST['project'][$i];
		// $project 	= '';
		//---
		if (!empty($user_name)) {
			//---
			$user_name = trim($user_name);
			$email     = trim($email);
			$wiki      = trim($wiki);
			$project   = trim($project);
			//---
			sql_add_user($user_name, $email, $wiki, $project, $ido);
			//---
		};
	};
};
//---
?>
