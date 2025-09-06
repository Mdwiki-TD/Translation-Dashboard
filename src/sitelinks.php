<?php
//---
include_once __DIR__ . '/include_all.php';
include_once __DIR__ . '/header.php';
//---

use function TD\Render\TestPrint\test_print;
use function Tables\TablesDir\open_td_Tables_file;

// Get request parameters with defaults
$site = htmlspecialchars($_GET['site'] ?? 'all', ENT_QUOTES, 'UTF-8');
$heads_limit = filter_input(INPUT_GET, 'heads_limit', FILTER_VALIDATE_INT, [
	'options' => ['default' => 50, 'min_range' => 1, 'max_range' => 1000]
]);

$title_limit = filter_input(INPUT_GET, 'title_limit', FILTER_VALIDATE_INT, [
	'options' => ['default' => 150, 'min_range' => 10, 'max_range' => 1000]
]);

$items_with_no_links = isset($_GET["items_with_no_links"]) ? "checked" : "";

// Generate form inputs
function generateFormInputs(array $params, string $items_with_no_links): string
{
	$html = <<<HTML
		<div style='box-sizing:border-box;'>
			<form class='form-inline' action='sitelinks.php' method='get'>
				<div class="row">
		HTML;

	foreach ($params as $key => $tab) {
		$value = $tab['value'];
		$type = $tab['type'];
		$html .= <<<HTML
        <div class="col-md-3">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">$key</span>
                </div>
                <input class="form-control w-50" type="$type" id="$key" name="$key" value="$value">
            </div>
        </div>
        HTML;
	}
	$html .= <<<HTML
				<div class="col-md-3">
					<div class="form-check form-switch">
						<input class="form-check-input" type="checkbox" id="switch2" name="items_with_no_links" role="switch" value="1" $items_with_no_links>
						<label class="check-label" for="switch2">&nbsp;Items with no links</label>
					</div>
				</div>
			</div>
			<input type='submit' value='Submit' class='btn btn-outline-primary' />
		</form>
	</div>
	HTML;

	return $html;
}

// Form parameters
$params = [
	'site' => ["type" => "text", "value" => $site],
	'heads_limit' => ["type" => "number", "value" => $heads_limit],
	'title_limit' => ["type" => "number", "value" => $title_limit],
];

echo generateFormInputs($params, $items_with_no_links);

$file2 = "jsons/sitelinks.json";
$data2 = open_td_Tables_file($file2);

$heads_all = array_diff($data2["heads"], ["commons"]);
$qids_all = $data2['qids'] ?? [];

// Sort QIDs by sitelinks count
uasort($qids_all, fn($a, $b) => count($b['sitelinks']) <=> count($a['sitelinks']));

test_print("$file2: qids_all: " . count($qids_all));
test_print("$file2: heads_all: " . count($heads_all));

$heads = array_slice($heads_all, 0, $heads_limit);
$qids_o = array_slice($qids_all, 0, $title_limit);

$len_heads_all = count($heads_all);
$len_qids_all = count($qids_all);
$len_items_with_site = 0;
$with_site_note = "";
$notitle = true;

// Filter QIDs based on user selection
if ($items_with_no_links) {
	$heads = [];
	$qids_o = array_filter($qids_all, fn($tab) => count($tab['sitelinks']) == 0);
} elseif (!empty($site) && $site != "all") {
	$notitle = false;
	$heads = [$site];
	$len_items_with_site = count(array_filter($qids_all, fn($tab) => $tab['sitelinks'][$site] ?? false));
	$no_site_link = $len_qids_all - $len_items_with_site;
	$with_site_note = " (with site: $len_items_with_site, no site link: $no_site_link)";
}

echo <<<HTML
    <div style='box-sizing:border-box;'>
        <h3>Heads: $len_heads_all, Qids: $len_qids_all $with_site_note</h3>
    </div>
	<div style='box-sizing:border-box;'>
	<table class='table table-striped compact sortable' id='table-1'>
		<thead>
			<tr>
				<th>#</th>
				<th>qid</th>
				<th>links</th>
				<th>mdtitle</th>
HTML;

foreach ($heads as $head) {
	echo "<th>$head</th>";
}

echo "</tr></thead><tbody>";

$i = 0;
foreach ($qids_o as $qid => $tab) {
	$i++;
	$mdtitle = $tab['mdtitle'] ?? "";
	$count_links = count($tab['sitelinks']);
	echo <<<HTML
    <tr>
        <td>$i</td>
        <td><a href='https://wikidata.org/wiki/$qid'>$qid</a></td>
        <td>$count_links</td>
        <td><a href='https://mdwiki.org/wiki/$mdtitle'>$mdtitle</a></td>
    HTML;

	foreach ($heads as $head) {
		$value = $tab['sitelinks'][$head] ?? '';
		$link = $value ? "<a href='https://$head.wikipedia.org/wiki/$value'>" . ($notitle ? 'O' : $value) . "</a>" : '';
		echo "<td>$link</td>";
	}
	echo "</tr>";
}

echo "</tbody></table>";
include_once __DIR__ . '/footer.php';
