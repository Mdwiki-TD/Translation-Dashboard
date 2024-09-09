<?php
//---
use function Actions\MdwikiSql\execute_query;
//---
if (isset($_POST['del'])) {
	for($i = 0; $i < count($_POST['del']); $i++ ) {
		$del	= $_POST['del'][$i];
		//---
		if (!empty($del)) {
			$qua2 = "DELETE FROM coordinator WHERE id = ?";
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
			$qua = "INSERT INTO coordinator (user) SELECT ? WHERE NOT EXISTS (SELECT 1 FROM coordinator WHERE user = ?)";
			//---
			execute_query($qua, $params=[$user, $user]);
		};
	};
};
//---
?>
