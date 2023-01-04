
<h4>Translation campaigns:</h4>
<form action="coordinator.php?ty=Campaigns" method="POST">
	<input name='ty' value="Campaigns" hidden/>
	  <div class="form-group">
  <table class='table'>
	  <tr>
		<th>category</th>
		<th>display</th>
		<th>depth</th>
	  </tr>
	<tbody id="tab_logic">

<?php
//---
// if (isset($_POST['cat'])) {
for($i = 0; $i < count($_POST['cat']); $i++ ){
	$cat = $_POST['cat'][$i];
	$dis = $_POST['dis'][$i];
	$ido = $_POST['id'][$i];
	$dep = $_POST['dep'][$i];
	//---
	$qua = "INSERT INTO categories (category, display, depth) SELECT '$cat', '$dis', '$dep'
	WHERE NOT EXISTS (SELECT 1 FROM categories WHERE category = '$cat')";
	//---	
	if (isset($ido)) {
		$qua = "UPDATE categories 
		SET 
		display = '$dis',
		category = '$cat',
		depth = '$dep'
		WHERE id = '$ido'
		";
	};
	quary2($qua);
};
// };
//---
$uuux = '';
//---
// ALTER TABLE `categories` ADD `depth` INT(2) NULL DEFAULT NULL AFTER `display`;
// ALTER TABLE categories DROP depth;
//---
$qq = quary2('select id, category, display, depth from categories;');
//---
$numb = 0;
//---
foreach ( $qq AS $Key => $table ) {
	$numb += 1;
	$id 		= $table['id'];
	$category 	= $table['category'];
	$display 	= $table['display'];
	$depth		= $table['depth'];
	$qu = "DELETE FROM categories WHERE id = '$id'";
    //---
    $qua = rawurlencode( $qu );
    $urle = "sql.php?code=$qua&pass=yemen&raw=66";
    //---
	echo "
	<tr>
	  <td>
	  	<input name='cat[]$numb' id='cat[]$numb' value='$category'/>
	  	<input name='id[]$numb' id='id[]$numb' value='$id' hidden/>
	  </td>
	  <td><input name='dis[]$numb' id='dis[]$numb' value='$display'/></td>
	  <td><input name='dep[]$numb' id='dep[]$numb' value='$depth'/></td>
	  <td><a href='$urle' target='_blank' onclick='refr()'>delete</a></td>
	</tr>";
};
//---
?>

</tbody>
</table>
  <button type="submit" class="btn btn-success">Submit</button>
</form>
<span role='button' id="add_row" class="btn btn-info" style="position: absolute; right: 130px;" onclick='add_row()'>New row</span>
</div>
<script type="text/javascript">
var i = 1;
function add_row() {
	var ii = $('#tab_logic >tr').length + 1;
	var e = "<tr><td><input name='cat[]" + ii + "'/></td><td><input name='dis[]" + ii + "'/></td><td><input name='dep[]" + ii + "' value='0'/></td><td></td></tr>";

	$('#tab_logic').append(e);
	i++;
};
function refr() {
	setTimeout(function(){
   window.location.reload(1);
}, 2000);
}

</script>


  </div>