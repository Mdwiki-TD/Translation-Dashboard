<?php
//---
if (isset($_POST['se'])) {
	// echo "<pre>" . var_export($_POST, true) . "</pre>";
    $se   = $_POST['se'] ?? [];
    foreach ($se as $index => $n) {
        //---
        $id       = $_POST["id_$n"] ?? '';
        $title    = $_POST["title_$n"] ?? '';
        $lead     = $_POST["lead_$n"] ?? '';
        $full     = $_POST["full_$n"] ?? '';
        //---
        if ($title == '') continue;
        //---
        $re = insert_to_translate_type($title, $lead, $full, $tt_id=$id);
		//---
		// echo "<br>added: title: $title, lead: $lead, full: $full";
		//---
    }
}
//---
if (isset($_POST['add'])) {
	// var_export($_POST['add']);
	for($i = 0; $i < count($_POST['add']); $i++ ){
		//---
		$title 	= $_POST['title'][$i] ?? '';
		$lead 	= $_POST['lead'][$i] ?? 0;
		$full 	= $_POST['full'][$i] ?? 0;
		//---
        if ($title == '') continue;
        //---
        $re = insert_to_translate_type($title, $lead, $full);
		//---
	};
};
//---
echo <<<HTML
	<div class='alert alert-success' role='alert'>Translate Type Saved...<br>
		return to Translate Type page in 2 seconds
	</div>
	<meta http-equiv='refresh' content='2; url=coordinator.php?ty=tt'>
HTML;
//---
