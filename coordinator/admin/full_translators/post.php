<?php
//---
use function Actions\MdwikiSql\execute_query;
//---
if (isset($_POST['del'])) {
	for($i = 0; $i < count($_POST['del']); $i++ ) {
		$del	= $_POST['del'][$i];
		//---
		if (!empty($del)) {
			$qua2 = "DELETE FROM full_translators WHERE id = ?";
			execute_query($qua2, $params=[$del]);
		};
	};
};
//---
if (isset($_POST['user'])) {
	for($i = 0; $i < count($_POST['user']); $i++ ) {
		$ido  	= $_POST['id'][$i] ?? '';
		$user  	= $_POST['user'][$i] ?? '';
		//---
		$user = trim($user);
		//---
		if (!empty($user) && empty($ido)) {
			$qua = "INSERT INTO full_translators (user) SELECT ? WHERE NOT EXISTS (SELECT 1 FROM full_translators WHERE user = ?)";
			//---
			execute_query($qua, $params=[$user, $user]);
		};
	};
};
//---
