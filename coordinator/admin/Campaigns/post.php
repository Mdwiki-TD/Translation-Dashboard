<?php
//---
use function Actions\MdwikiSql\execute_query;
//---
if (isset($_POST['del'])) {
	for($i = 0; $i < count($_POST['del']); $i++ ) {
		$del	= $_POST['del'][$i];
		//---
		if (!empty($del)) {
			$qua2 = "DELETE FROM categories WHERE id = ?";
			execute_query($qua2, [$del]);
		};
	};
};
//---
$default_cat = $_POST['default_cat'] ?? '';
//---
if (isset($_POST['cats'])) {
	for($i = 0; $i < count($_POST['cats']); $i++ ){
		$cat1= $_POST['cats'][$i];
		$cat2= $_POST['cat2'][$i];
		$camp = $_POST['camp'][$i];
		$ido = $_POST['id'][$i];
		$ido = (isset($ido)) ? $ido : '';
		$dep = $_POST['dep'][$i];
		//---
		// $def = $_POST['def'][$i];
		$def = ($default_cat == $ido) ? 1 : 0;
		//---
		// $qua = "INSERT INTO categories (category, campaign, depth, def) SELECT ?, ?, ?, ?
		// WHERE NOT EXISTS (SELECT 1 FROM categories WHERE category = ?)";
		// $params = [$cat1, $camp, $dep, $def, $cat1];
		//---
		$qua = "INSERT INTO categories (category, campaign, depth, def, category2) SELECT ?, ?, ?, ?, ?";
		$params = [$cat1, $camp, $dep, $def, $cat2];
		//---
		if (!empty($ido)) {
			$qua = "UPDATE categories
			SET
				campaign = ?,
				category = ?,
				category2 = ?,
				depth = ?,
				def = ?
			WHERE
				id = ?
			";
			$params = [$camp, $cat1, $cat2, $dep, $def, $ido];
		};
		//---
		if (isset($_REQUEST['test'])) {
			echo "<br>$qua<br>";
		};
		//---
		execute_query($qua, $params);
	};
	if ($_REQUEST['test'] == 'dd') {
		exit;
	};
};
//---
?>
