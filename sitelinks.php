<?PHP
//--------------------
require('header.php');
//--------------------
echo "
</div>
    <div style='boxSizing:border-box;' align=left>
    <table class='table table-striped sortable alignleft'>
        <thead>
            <tr>";
//====
$file2 = "Tables/sitelinks.json";
//--------------------
// open json file
$json2 = file_get_contents($file2);
$data2 = json_decode($json2, true);
//--------------------
$heads = $data2["heads"];
// limit heads to 20
$heads = array_slice($heads, 0, 20);
//====
print "
<th>qid</th>
<th>mdtitle</th>
";
//====
foreach ($heads as $head) {
    print "
                <th>$head</th>";
}
print "     </tr>
        </thead>
        <tbody>";
//--------------------
$qids = $data2['qids'];
//====
/*tab = {
        "heads": ["arwiki"],
        "qids": {
            "Q1": { "mdtitle": "test","sitelinks": {"arwiki": "test"}}
        }
    }
    */
//====
// show 100 row only
$i = 0;
foreach ($qids as $qid => $tab) {
    $i = $i + 1;
    if ($i > 100) {
        break;
    }
    print "
            <tr>";
    //--------------------
    $mdtitle = $tab['mdtitle'];

    print "<th><a href='https://wikidata.org/wiki/$qid'>$qid</a></th>";
    print "<th><a href='https://mdwiki.org/wiki/$mdtitle'>$mdtitle</a></th>";
    //--------------------
    foreach ($heads as $head) {
        $value = $tab['sitelinks'][$head];
        $link = "<a href='https://$head.wikipedia.org/wiki/$value'>$value</a>";
        print "<td>$link</td>
        ";
    }
    print "
            </tr>
            ";
}
//--------------------
print "
</table>
</div>
<div>
";
//--------------------
require('foter.php');
//--------------------
?>