
<?php
//---
if (user_in_coord == false) {
	echo "<meta http-equiv='refresh' content='0; url=index.php'>";
	exit;
};
//---
function make_edit_icon($id, $title, $qid) {
	//---
    $edit_params = array(
		'id'   => $id,
		'title'  => $title,
		'nonav'  => 1,
		'qid'  => $qid
	);
    //---
    $edit_url = "coordinator.php?ty=qids/edit_qid&" . http_build_query( $edit_params );
    //---
	$onclick = 'pupwindow1("' . $edit_url . '")';
    //---
    return <<<HTML
    	<a class='btn btn-primary btn-sm' onclick='$onclick'>Edit</a>
    HTML;
}
//---
$testin = (($_REQUEST['test'] ?? '') != '') ? "<input name='test' value='1' hidden/>" : "";
//---
$dis = $_GET['dis'] ?? 'all';
//---
echo <<<HTML
<script>$('#qidsload').addClass('active');</script>
	<div class='card-header'>
		<div class='row'>
			<div class='col-md-3'>
				<h4>Qids:</h4>
			</div>
			<div class='col-md-3'>
				<!-- only display empty qids -->
				<a class='btn btn-outline-secondary' href="coordinator.php?ty=qids/load&dis=empty">Only Empty</a>
			</div>
			<div class='col-md-3'>
				<a class='btn btn-outline-secondary' href="coordinator.php?ty=qids/load&dis=all">All</a>				
			</div>
		</div>
	</div>
	<div class='card-body'>
		<table class='table table-striped compact table-mobile-responsive table-mobile-sided sortable2' style='width: 90%;'>
			<thead>
				<tr>
					<th>#</th>
					<th>Title</th>
					<th>Qid</th>
					<th>Edit</th>
				</tr>
			</thead>
			<tbody id="tab_logic">
HTML;
//---
$uuux = '';
//---
$qua = ($dis == 'all') ? 'select id, title, qid from qids;' : "select id, title, qid from qids where qid = '';";
//---
$qq = execute_query($qua);
//---
$numb = 0;
//---
foreach ( $qq AS $Key => $table ) {
	$numb += 1;
	$id 	= $table['id'];
	$title 	= $table['title'];
	$qid 	= $table['qid'];
    //---
	$edit_icon = make_edit_icon($id, $title, $qid);
    //---
	$md_title = make_mdwiki_title($title);
    //---
	echo <<<HTML
	<tr>
		<th data-content="#" data-sort="$numb">
			$numb
		</th>
		<td data-content="title" data-sort="$title">
			$md_title
		</td>
		<td data-content="qid" data-sort="$qid">
			<a target='_blank' href='https://wikidata.org/wiki/$qid'>$qid</a>
		</td>
		<td data-content="Edit">
			$edit_icon
		</td>
	</tr>
	HTML;
};
//---
echo <<<HTML
	</tbody>
	</table>

<form action="coordinator.php?ty=qids/post&nonav=1" method="POST">
	$testin
	<input name='ty' value="qids/post" hidden/>
	<div id='qidstab' style='display: none;'>
		<table class='table table-striped compact table-mobile-responsive table-mobile-sided' style='width: 90%;'>
			<thead>
				<tr>
					<th>#</th>
					<th>Title</th>
					<th>Qid</th>
				</tr>
			</thead>
			<tbody id="tab_new">

			</tbody>
		</table>
	</div>
	<span role='button' id="add_row" class="btn btn-info" style="position: absolute; right: 130px;" onclick='add_row()'>New row</span>
	<button type="submit" class="btn btn-success">Submit</button>
</form>
HTML;
?>
<script type="text/javascript">
var i = 1;
function add_row() {
	$('#qidstab').show();
	var ii = $('#tab_new >tr').length + 1;
	var e = "<tr>";
	e = e + "<td>" + ii + "</td>";
	e = e + "<td><input name='add_qids[]" + ii + "' placeholder='title" + ii + "'/></td>";
	e = e + "<td><input name='qid[]" + ii + "' placeholder='qid" + ii + "'/></td>";
	e = e + "</tr>";
	$('#tab_new').append(e);
	i++;
};
</script>
</div>