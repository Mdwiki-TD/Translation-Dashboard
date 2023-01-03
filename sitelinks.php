<?PHP
//---
require('header.php');
//---
$site = $_REQUEST["site"] ? $_REQUEST["site"] : "all";
$title_limit = $_REQUEST["title_limit"] ? number_format($_REQUEST["title_limit"]) : 150;
//---
echo "
</div>
<div style='boxSizing:border-box;' align=left>
    <form action='sitelinks.php' method='get'>
        <label>Site:</label>
        <input type='text' name='site' value='$site' />
        <label>Title Limit:</label>
        <input type='text' name='title_limit' value='$title_limit' />
        <input type='submit' value='Submit' />
    </form>
</div>
<div style='boxSizing:border-box;' align=left>
<table class='table table-striped sortable alignleft' id='table-1'>
    <thead>
        <tr>";
//---
$file2 = "Tables/sitelinks.json";
//---
// open json file
$json2 = file_get_contents($file2);
$data2 = json_decode($json2, true);
//---
$heads = $data2["heads"];
# remove commons from heads
$heads = array_diff($heads, array("commons"));
$qids = $data2['qids'];
// limit to 20
$heads = array_slice($heads, 0, 50);
// limit to 150
$qids = array_slice($qids, 0, $title_limit);
//---
$notitle = true;
//---
if ($site != "" && $site != "all") {
    $notitle = false;
    $heads = array();
    $heads[] = $site;
    $qids = $data2['qids'];
}
//---
print "
    <th>#</th>
    <th>qid</th>
    <th>mdtitle</th>
";
//---
foreach ($heads as $head) {
    print "
                <th>$head</th>";
}
print "     </tr>
        </thead>
        <tbody>";
//---
//---
/*tab = {
        "heads": ["arwiki"],
        "qids": {
            "Q1": { "mdtitle": "test","sitelinks": {"arwiki": "test"}}
        }
    }
    */
//---
// show 100 row only
$i = 0;
foreach ($qids as $qid => $tab) {
    $i = $i + 1;
    print "
            <tr>";
    //---
    $mdtitle = $tab['mdtitle'];

    print "<td>$i</td>";
    print "<td><a href='https://wikidata.org/wiki/$qid'>$qid</a></td>";
    print "<td><a href='https://mdwiki.org/wiki/$mdtitle'>$mdtitle</a></td>";
    //---
    foreach ($heads as $head) {
        $value = isset($tab['sitelinks'][$head]) ? $tab['sitelinks'][$head] : '';
        $link = "";
        if ($value != "") {
            $link = "<a href='https://$head.wikipedia.org/wiki/$value'>$value</a>";
            if ($notitle) {
                $link = "<a href='https://$head.wikipedia.org/wiki/$value'>O</a>";
            }
        };
        print "<td>$link</td>
        ";
    }
    print "
            </tr>
            ";
}
//---
print '
</table>
</div>
<div>
';
//---
if ($_REQUEST['test'] != '' ) echo "<br>load " . str_replace ( __dir__ , '' , __file__ ) . " true.";
//---
require('foter.php');
//---
?>