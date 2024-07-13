<?php
//---
use function Actions\MdwikiSql\execute_query;
//---
if (isset($_POST['add_qids'])) {
	echo "add_qids";
	for($i = 0; $i < count($_POST['add_qids']); $i++ ){
		$title = $_POST['add_qids'][$i];
		$qid   = $_POST['qid'][$i];
		//---
		$qua = "INSERT INTO qids (title, qid)
			SELECT ?, ?
		WHERE NOT EXISTS (SELECT 1 FROM qids WHERE qid = ?)";
		//---
		$params = [$title, $qid, $qid];
		//---
		execute_query($qua, $params);
	};
};
//---
echo <<<HTML
	<div class='alert alert-success' role='alert'>Qid Saved...<br>
		return to qids page in 2 seconds
	</div>
	<meta http-equiv='refresh' content='2; url=coordinator.php?ty=qids'>
HTML;
//---
