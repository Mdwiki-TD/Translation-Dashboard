<?PHP
//---
require('tables.php'); 
require('getcats.php');
include_once('functions.php');
//---
$cat = $_REQUEST['cat'] ?? 'RTT';
$cats_titles = array();
//---
foreach ( execute_query('select category from categories;') AS $k => $tab ) $cats_titles[] = $tab['category'];
//---
$d33 = "
<div class='col-md-3 col-sm-3'>
    <div class='form-group'>
        <div class='input-group'>
            <div class='input-group-prepend'>
                <span class='input-group-text'>%s</span>
            </div>
                %s
        </div>
    </div>
</div>
";
//---
$y1 = makeDropdown($cats_titles, $cat, 'cat', '');
$uuu = sprintf($d33, 'Category:', $y1);
//---
$fa = "
<div class='card-header'>
    <form method='get' action='coordinator.php'>
        <input name='ty' value='stat' hidden/>
        <div class='row'>
            <div class='col-md-2'>
                <h4>Status:</h4>
            </div>
            $uuu    
            <div class='aligncenter col-md-2'><input class='btn btn-primary' type='submit' name='start' value='Filter' /></div>
        </div>
    </form>
</div>
<div class='cardbody'>";
//---
$table = "
	<table class='table table-striped compact soro'>
	<thead>
		<tr>
			<th>#</th>
			<th>title</th>
			<th>qid</th>
			<th>lead word</th>
			<th>all word</th>
			<th>ref</th>
			<th>all ref</th>
			<th>Importance</th>
			<th>enwiki views</th>
		</tr>
	</thead>
	<tbody>";
//---
$titles = get_mdwiki_cat_members( $cat, $use_cash=true, $depth=1 );
//---
$no_qid = 0;
$no_word = 0;
$no_allword = 0;
$no_ref = 0;
$no_allref = 0;
$no_Importance = 0;
$no_pv = 0;
$i = 0;
//---
foreach ($titles as $title) {
    $i = $i + 1;
    //---
    $qid = $sql_qids[$title] ?? "";
    //---
    if ($qid == '') $no_qid +=1;
    //---
    $qidurl = ($qid != '') ? "<a href='https://wikidata.org/wiki/$qid'>$qid</a>" : '';
    //---
    $word = $Words_table[$title] ?? 0; 
    //---
    $allword = $All_Words_table[$title] ?? 0;
    if ($word == 0) $no_word +=1;
    if ($allword == 0) $no_allword +=1;
    //---
    $refs = $Lead_Refs_table[$title] ?? 0;
    //---
    $all_refs = $All_Refs_table[$title] ?? 0;
    //---
    if ($refs == 0) $no_ref +=1;
    if ($all_refs == 0) $no_allref +=1;
    $asse = $Assessments_table[$title] ?? '';
    if (!isset($Assessments_table[$title])) $no_Importance +=1;
    //---
	$pv = $enwiki_pageviews_table[$title] ?? 0; 
    if (!isset($enwiki_pageviews_table[$title])) $no_pv +=1;
    //---
    //---
    $table .= "
    <tr>
        <td>$i</td>
        <td><a href='https://mdwiki.org/wiki/$title'>$title</a></td>
        <td>$qidurl</td>
        <td>$word</td>
        <td>$allword</td>
        <td>$refs</td>
        <td>$all_refs</td>
        <td>$asse</td>
        <td><a href='https://en.wikipedia.org/w/api.php?action=query&prop=pageviews&titles=$title&redirects=1&pvipdays=30'>$pv</a></td>
    </tr>
    ";
}
//---
$table .= "</table>";
//---
$with_q = $i - $no_qid;
$with_word = $i - $no_word;
$with_allword = $i - $no_allword;
$with_ref = $i - $no_ref;
$with_allref = $i - $no_allref;
$with_Importance = $i - $no_Importance;
$with_pv = $i - $no_pv;
//---
$tabo = "
	<div class='col-md'>
		<div class='card'>
			<div class='card-body'>
				<table class='table table-sm'>
					<tr>
						<th>Type</th>
						<th>With</th>
						<th>Without</th>
					</tr>
						%s
				</table>
			</div> 
		</div>
	</div>";
//---
echo $fa;
echo "<div class='row'> ";
//---
echo sprintf($tabo, "
	<tr><td>qid</td><td>$with_q</td><td>$no_qid</td></tr>						
	<tr><td>enwiki views</td><td>$with_pv</td><td>$no_pv</td></tr>
	<tr><td>Importance</td><td>$with_Importance</td><td>$no_Importance</td></tr>");
//---
echo sprintf($tabo, "	
	<tr><td>word</td><td>$with_word</td><td>$no_word</td></tr>
	<tr><td>allword</td><td>$with_allword</td><td>$no_allword</td></tr>");
//---
echo sprintf($tabo, "
	<tr><td>ref</td><td>$with_ref</td><td>$no_ref</td></tr>
	<tr><td>allref</td><td>$with_allref</td><td>$no_allref</td></tr>");
//---
echo "</div>";
//---
echo $table;
//---
?>
<?PHP
//---
print '
</div>
</div>
';
//---
?>