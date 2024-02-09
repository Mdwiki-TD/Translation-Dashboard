<?php
//---
/*

INSERT INTO translate_type (tt_title, tt_lead, tt_full) SELECT DISTINCT q.title, 1, 0 from qids q 
	WHERE q.title not in (SELECT tt_title FROM translate_type)

*/
//---
require 'getcats.php';
include_once 'functions.php';
//---
$cat = $_REQUEST['cat'] ?? 'All';
$testin = (($_REQUEST['test'] ?? '') != '') ? "<input name='test' value='1' hidden/>" : "";
//---
function filter_stat($cat) {
	global $cat_to_camp;
	// array keys
	$cats_titles = array_keys($cat_to_camp);
	//---
	$d33 = <<<HTML
		<div class="input-group">
			<span class="input-group-text">%s</span>
			%s
		</div>
	HTML;
	//---
	$y1 = makeDropdown($cats_titles, $cat, 'cat', 'All');
	$uuu = sprintf($d33, 'Category:', $y1);
	//---
    return $uuu;
}
//---
$uuu = filter_stat($cat);
//---
$new_titles = array();
$full_translates_tab = array();
//---
$translate_type_sql = <<<SQL
    SELECT tt_id, tt_title, tt_lead, tt_full
	FROM translate_type
SQL;
//---
foreach ( execute_query($translate_type_sql) AS $k => $tab ) {
	$full_translates_tab[$tab['tt_title']] = ['id' => $tab['tt_id'], 'lead' => $tab['tt_lead'], 'full' => $tab['tt_full']];
}
//---
$cat_titles = array();
//---
if ($cat == 'All') {
	foreach ( execute_query('SELECT DISTINCT title from qids WHERE title not in (SELECT tt_title FROM translate_type)') AS $Key => $gg ) {
		if (!in_array($gg['title'], $full_translates_tab)) {
			$new_titles[] = $gg['title'];
		}
	};
	$cat_titles = array_keys($full_translates_tab);
} else {
	$cat_titles = get_mdwiki_cat_members($cat, $use_cash=true, $depth=1);
}
//---
echo <<<HTML
	<script>
		$('#tt_load').addClass('active');
		$("#tt_load").closest('.mb-1').find('.collapse').addClass('show');
	</script>
	<div class='card-header'>
		<form action="coordinator.php?ty=tt" method="GET">
			$testin
			<input name='ty' value="tt" hidden/>
			<div class='row'>
				<div class='col-md-3'>
					<h4>Translate Type:</h4>
				</div>
				<div class='col-md-3'>
					$uuu
				</div>
				<div class='aligncenter col-md-2'><input class='btn btn-primary' type='submit' name='start' value='Filter' /></div>
			</div>
		</form>
	</div>
	<div class='card-body'>
	<table id='em' class='table table-striped compact table-mobile-responsive table-mobile-sided'>
		<thead>
			<tr>
				<th>#</th>
				<th>id</th>
				<th>Title</th>
				<th>Lead</th>
				<th>Full</th>
				<th>Edit</th>
			</tr>
		</thead>
		<tbody id="tab_ma">
	HTML;
//---
function make_edit_icon($id, $title, $full, $lead) {
	//---
    $edit_params = array(
		'id'   => $id,
		'title'  => $title,
		'nonav'  => 1,
		'lead'  => $lead,
		'full'  => $full
	);
    //---
    $edit_url = "coordinator.php?ty=tt/edit_translate_type&" . http_build_query( $edit_params );
    //---
	$onclick = 'pupwindow1("' . $edit_url . '")';
    //---
    return <<<HTML
    	<a class='btn btn-outline-primary btn-sm' onclick='$onclick'>Edit</a>
    HTML;
}
//---
function make_row($id, $title, $lead, $full, $numb) {
	$edit_icon = make_edit_icon($id, $title, $full, $lead);
    //---
	$md_title = make_mdwiki_title($title);
    //---
	$lead_checked = ($lead == 1 || $lead == "1") ? 'checked' : '';
	$full_checked = ($full == 1 || $full == "1") ? 'checked' : '';
    //---
	return <<<HTML
	<tr>
		<th data-content="#" data-sort="$numb">
			$numb
		</th>
		<th data-content="#" data-sort="$id">
			$id
		</th>
		<td data-content="title" data-sort="$title">
			$md_title
		</td>
		<td data-content='Lead' data-sort='$lead'>
			<div class='form-check form-switch'>
				<input class='form-check-input' type='checkbox' name='lead_$numb' value='1' $lead_checked disabled>
			</div>
		</td>
		<td data-content='Full' data-sort='$full'>
			<div class='form-check form-switch'>
				<input class='form-check-input' type='checkbox' name='full_$numb' value='1' $full_checked disabled>
			</div>
		</td>
		<td data-content="Edit">
			$edit_icon
		</td>
	</tr>
	HTML;
}
//---
$numb = 0;
//---
foreach ( $cat_titles as $title ) {
	//---
	if (in_array($title, $new_titles)) continue;
	//---
	$numb += 1;
	//---
	$table = $full_translates_tab[$title] ?? [];
	//---
	$id			= $table['id'] ?? '';
	$lead 		= $table['lead'] ?? 1;
	$full		= $table['full'] ?? 0;
    //---
	echo make_row($id, $title, $lead, $full, $numb);
	//---
};
//---
echo <<<HTML
		</tbody>
	</table>

	<form action="coordinator.php?ty=tt/post" method="POST">
	$testin
	<input name='ty' value="tt/post" hidden/>
	<div id='tt_table' class="form-group" style='display: none;'>
		<table class='table table-striped compact table-mobile-responsive table-mobile-sided' style='width: 90%;'>
			<thead>
				<tr>
					<th>#</th>
					<th>Title</th>
					<th>Lead</th>
					<th>Full</th>
				</tr>
			</thead>
			<tbody id="tab_new">

			</tbody>
		</table>
	</div>
	<span role='button' id="add_row" class="btn btn-info" style="position: absolute; right: 130px;" onclick='add_row()'>New row</span>
	<button id="submit_bt" type="submit" class="btn btn-success" style='display: none;'>Submit</button>
</form>
HTML;
?>
<script type="text/javascript">
	var i = 1;
	function add_row() {
		$('#submit_bt').show();
		$('#tt_table').show();
		var ii = $('#tab_new >tr').length + 1;
		var e = "<tr>";
		e = e + "<td>" + ii + "</td>";
		e = e + "<input type='hidden' name='add[]" + ii + "'/>";
		e = e + "<td><input name='title[]" + ii + "'/></td>";

		e = e + "<td data-content='Lead'><div class='form-check form-switch'>";
		e = e + "<input type='text' name='lead[]" + ii + "' value='0'/>";
		// e = e + "<input size='2' class='form-check-input' type='checkbox' name='lead[]" + ii + "' value='1'>";
		e = e + "</div></td>";

		e = e + "<td data-content='Full'><div class='form-check form-switch'>";
		e = e + "<input type='text' name='full[]" + ii + "' value='0'/>";
		// e = e + "<input size='2' class='form-check-input' type='checkbox' name='full[]" + ii + "' value='1'>";
		e = e + "</div></td>";

		e = e + "</tr>";
		$('#tab_new').append(e);
		i++;
	};

	$(document).ready( function () {
		var t = $('#em').DataTable({
		// order: [[5	, 'desc']],
		// paging: false,
		lengthMenu: [[250, 500], [250, 500]],
		// scrollY: 800
		});
	} );

</script>

</div>