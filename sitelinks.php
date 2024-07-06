<?php
include_once __DIR__ . '/header.php';

$site = isset($_REQUEST["site"]) ? $_REQUEST["site"] : "all";
$title_limit = isset($_REQUEST["title_limit"]) ? number_format($_REQUEST["title_limit"]) : 150;

echo <<<HTML
</div>
<div style='box-sizing:border-box;'>
    <form action='sitelinks.php' method='get'>
        <label>Site:</label>
        <input type='text' name='site' value='$site' />
        <label>Title Limit:</label>
        <input type='text' name='title_limit' value="$title_limit" />
        <input type='submit' value='Submit' />
    </form>
</div>
<div style='box-sizing:border-box;'>
<table class='table table-striped compact sortable' id='table-1'>
    <thead>
        <tr>
            <th>#</th>
            <th>qid</th>
            <th>mdtitle</th>
HTML;

$file2 = "Tables/jsons/sitelinks.json";
$json2 = file_get_contents($file2);
$data2 = json_decode($json2, true);

$heads = array_diff($data2["heads"], array("commons"));
$qids_o = $data2['qids'];

$heads = array_slice($heads, 0, 50);
$qids_o = array_slice($qids_o, 0, $title_limit);

$notitle = true;

if ($site != "" && $site != "all") {
    $notitle = false;
    $heads = array($site);
    $qids_o = $data2['qids'];
}

foreach ($heads as $head) {
    echo "<th>$head</th>";
}

echo "</tr></thead><tbody>";

$i = 0;
foreach ($qids_o as $qid => $tab) {
    $i++;
    echo "<tr>";
    $mdtitle = $tab['mdtitle'];

    echo "<td>$i</td>";
    echo "<td><a href='https://wikidata.org/wiki/$qid'>$qid</a></td>";
    echo "<td><a href='https://mdwiki.org/wiki/$mdtitle'>$mdtitle</a></td>";

    foreach ($heads as $head) {
        $value = $tab['sitelinks'][$head] ?? '';
        $link = "";

        if ($value != "") {
            $link = "<a href='https://$head.wikipedia.org/wiki/$value'>$value</a>";

            if ($notitle) {
                $link = "<a href='https://$head.wikipedia.org/wiki/$value'>O</a>";
            }
        }

        echo "<td>$link</td>";
    }

    echo "</tr>";
}

echo "</tbody></table></div><div>";
include_once __DIR__ . '/foter.php';
?>