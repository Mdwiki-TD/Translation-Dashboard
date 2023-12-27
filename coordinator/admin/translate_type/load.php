<?php
//---
/*

INSERT INTO translate_type (tt_title, tt_lead, tt_full) SELECT DISTINCT q.title, 1, 0 from qids q 
	WHERE q.title not in (SELECT tt_title FROM translate_type)

*/
//---
require 'getcats.php';
include_once('functions.php');
//---
$cat = $_REQUEST['cat'] ?? 'RTTILAE';
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
	<div class='card-header'>
		<form action="coordinator.php?ty=translate_type" method="GET">
			$testin
			<input name='ty' value="translate_type" hidden/>
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
HTML;
//---
echo <<<HTML
	<form action="coordinator.php?ty=translate_type" method="POST">
		$testin
		<input name='ty' value="translate_type" hidden/>
		<input name='cat' value="$cat" hidden/>
		<div class="form-group">
			<table id='em' class='table table-striped compact table-mobile-responsive table-mobile-sided'>
				<thead>
					<tr>
						<th>#</th>
						<th>Title</th>
						<th>Lead</th>
						<th>Full</th>
					</tr>
				</thead>
				<tbody id="tab_ma">
	HTML;
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
	$lead_checked = ($lead == 1 || $lead == "1") ? 'checked' : '';
	$full_checked = ($full == 1 || $full == "1") ? 'checked' : '';
    //---
	echo <<<HTML
	<tr>
	HTML;
	//---
	echo <<<HTML
		<input name='se[]' value='$numb' hidden/>
		<td data-order='$numb' data-content='#'>
			$numb
			<input name='id_$numb' value='$id' hidden/>
		</td>
		<td data-content='Title'>
			$title
			<input type='hidden' class='form-control' name='title_$numb' value="$title"/>
		</td>
		<td data-content='Lead' data-order='$lead'>
			<div class='form-check form-switch'>
				<input type='hidden' name='lead_$numb' value='0'>
				<input class='form-check-input' type='checkbox' name='lead_$numb' value='1' $lead_checked>
			</div>
		</td>
		<td data-content='Full' data-order='$full'>
			<div class='form-check form-switch'>
				<input type='hidden' name='full_$numb' value='0'>
				<input class='form-check-input' type='checkbox' name='full_$numb' value='1' $full_checked>
			</div>
		</td>
	HTML;
	//---
	echo <<<HTML
	</tr>
	HTML;
};
//---
?>

			</tbody>
		</table>
		<button type="submit" class="btn btn-success">Submit</button>
		<span role='button' id="add_row" class="btn btn-info" style="position: absolute; right: 130px;" onclick='add_row()'>New row</span>
	  </div>
</form>

<script type="text/javascript">

	function pupwindow(url) {
		window.open(url, 'popupWindow', 'width=850,height=550,scrollbars=yes');
	};

	var i = 1;
	function add_row() {
		var ii = $('#tab_ma >tr').length + 1;
		var e = "<tr>";
		e = e + "<td>" + ii + "</td>";
		e = e + "<input type='hidden' name='add[]" + ii + "'/>";
		e = e + "<td><input name='title[]" + ii + "'/></td>";

		e = e + "<td data-content='Lead'><div class='form-check form-switch'>";
		e = e + "<input size='2' class='form-input' name='lead[]" + ii + "' value='1'>";
		e = e + "</div></td>";

		e = e + "<td data-content='Full'><div class='form-check form-switch'>";
		e = e + "<input size='2' class='form-input' name='full[]" + ii + "' value='0'>";
		e = e + "</div></td>";

		e = e + "</tr>";
			
				
			
		$('#tab_ma').append(e);
		i++;
	};

	$(document).ready( function () {
		var t = $('#em').DataTable({
		// order: [[5	, 'desc']],
		// paging: false,
		lengthMenu: [[50, 100, 150], [50, 100, 150]],
		// scrollY: 800
		});
	} );

</script>

</div>