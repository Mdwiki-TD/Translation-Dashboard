<?PHP
//---
require('header.php');
//---
$site = $_REQUEST["site"] ? $_REQUEST["site"] : "all";
$title_limit = $_REQUEST["title_limit"] ? number_format($_REQUEST["title_limit"]) : 150;
//---
echo "
</div>
<div style='boxSizing:border-box;'>
    <form action='sitelinks.php' method='get'>
        <label>Site:</label>
        <input type='text' name='site' value='$site' />
        <label>Title Limit:</label>
        <input type='text' name='title_limit' value='$title_limit' />
        <input type='submit' value='Submit' />
    </form>
</div>
<div style='boxSizing:border-box;'>
<table class='table table-striped compact soro2' id='table-1'>
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
$qids_o = $data2['qids'];
// limit to 20
$heads = array_slice($heads, 0, 50);
// limit to 150
$qids_o = array_slice($qids_o, 0, $title_limit);
//---
$notitle = true;
//---
if ($site != "" && $site != "all") {
    $notitle = false;
    $heads = array();
    $heads[] = $site;
    $qids_o = $data2['qids'];
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
foreach ($qids_o as $qid => $tab) {
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
        $value = $tab['sitelinks'][$head] ?? '';
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

//---
require('foter.php');
//---
?>