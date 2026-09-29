<?php

namespace App\SiteLinks;

use function App\Render\TestPrint\test_print;
use function App\Tables\TablesDir\open_td_tables_file;
use App\Settings;

class SiteLinksController
{
	/**
	 * Main entry point: read input, load data, prepare the view, render.
	 */
	public function handleRequest(): void
	{
		$params = $this->getRequestParams();

		echo $this->renderForm($params);

		$data = $this->loadData();
		$view = $this->prepareView($params, $data);

		echo $this->renderSummary($view);
		echo $this->renderTable($view);
	}

	// ---------------------------------------------------------------
	// 1) Request input
	// ---------------------------------------------------------------

	private function getRequestParams(): array
	{
		// Get request parameters with defaults
		$site = trim((string)($_GET['site'] ?? 'all'));
		if ($site === '') {
			$site = 'all';
		}

		$headsLimit = filter_input(INPUT_GET, 'heads_limit', FILTER_VALIDATE_INT, [
			'options' => ['default' => 50, 'min_range' => 1, 'max_range' => 1000],
		]);

		$titleLimit = filter_input(INPUT_GET, 'title_limit', FILTER_VALIDATE_INT, [
			'options' => ['default' => 150, 'min_range' => 10, 'max_range' => 1000],
		]);

		return [
			'site'				=> $site,
			'heads_limit'		 => $headsLimit ?: 50,
			'title_limit'		 => $titleLimit ?: 150,
			'items_with_no_links' => isset($_GET['items_with_no_links']),
		];
	}

	// ---------------------------------------------------------------
	// 2) Data loading
	// ---------------------------------------------------------------

	private function loadData(): array
	{
		$tablesPath = Settings::getInstance()->TablesPath;
		$data = open_td_tables_file("$tablesPath/jsons/sitelinks.json");

		// "commons" is not a Wikipedia edition, so exclude it
		$heads = array_diff($data['heads'] ?? [], ['commons']);
		$qids  = $data['qids'] ?? [];

		// Sort QIDs by number of sitelinks (descending)
		uasort($qids, fn($a, $b) => count($b['sitelinks']) <=> count($a['sitelinks']));

		test_print("jsons/sitelinks.json: qids_all: " . count($qids));
		test_print("jsons/sitelinks.json: heads_all: " . count($heads));

		return ['heads' => $heads, 'qids' => $qids];
	}

	// ---------------------------------------------------------------
	// 3) View data (filtering and limiting)
	// ---------------------------------------------------------------

	private function prepareView(array $params, array $data): array
	{
		$headsAll = $data['heads'];
		$qidsAll  = $data['qids'];
		$site	 = $params['site'];

		// Default: apply the user limits
		$heads = array_slice($headsAll, 0, $params['heads_limit']);
		$qids  = array_slice($qidsAll, 0, $params['title_limit'], true);

		$lenQidsAll   = count($qidsAll);
		$withSiteNote = '';
		$notitle	  = true; // show "O" instead of the article title

		// Filter QIDs based on user selection
		if ($params['items_with_no_links']) {
			// Only items that have no sitelinks at all
			$heads = [];
			$qids  = array_filter($qidsAll, fn($tab) => count($tab['sitelinks']) === 0);
		} elseif ($site !== 'all') {
			// Single-site mode: show the article title for that site
			$notitle = false;
			$heads   = [$site];

			$lenWithSite = count(array_filter($qidsAll, fn($tab) => !empty($tab['sitelinks'][$site])));

			$noSiteLink  = $lenQidsAll - $lenWithSite;
			$withSiteNote = " (with site: $lenWithSite, no site link: $noSiteLink)";
		}

		return [
			'heads'		  => $heads,
			'qids'		   => $qids,
			'len_heads_all'  => count($headsAll),
			'len_qids_all'   => $lenQidsAll,
			'with_site_note' => $withSiteNote,
			'notitle'		=> $notitle,
		];
	}

	// ---------------------------------------------------------------
	// 4) Template rendering
	// ---------------------------------------------------------------

	private function renderForm(array $params): string
	{
		$fields = [
			'site'		=> ['type' => 'text',   'value' => $params['site']],
			'heads_limit' => ['type' => 'number', 'value' => $params['heads_limit']],
			'title_limit' => ['type' => 'number', 'value' => $params['title_limit']],
		];

		$checked = $params['items_with_no_links'] ? 'checked' : '';

		$html = <<<HTML
			<div style='box-sizing:border-box;'>
				<form class='form-inline' action='sitelinks.php' method='get'>
					<div class="row">
		HTML;

		foreach ($fields as $key => $field) {
			$type  = $field['type'];
			$value = htmlspecialchars((string)$field['value'], ENT_QUOTES, 'UTF-8');
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
							<input class="form-check-input" type="checkbox" id="switch2" name="items_with_no_links" role="switch" value="1" $checked>
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

	private function renderSummary(array $view): string
	{
		$lenHeads = $view['len_heads_all'];
		$lenQids  = $view['len_qids_all'];
		$note	 = $view['with_site_note'];

		return <<<HTML
		<div style='box-sizing:border-box;'>
			<h3>Heads: $lenHeads, Qids: $lenQids $note</h3>
		</div>
		HTML;
	}

	private function renderTable(array $view): string
	{
		$heads   = $view['heads'];
		$notitle = $view['notitle'];

		$html = <<<HTML
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
			$formatedHead = $this->e($head);
			$html .= "<th>$formatedHead</th>";
		}

		$html .= '</tr></thead><tbody>';

		$i = 0;
		foreach ($view['qids'] as $qid => $tab) {
			$i++;
			$html .= $this->renderRow($i, (string)$qid, $tab, $heads, $notitle);
		}

		return $html . '</tbody></table></div>';
	}

	private function renderRow(int $i, string $qid, array $tab, array $heads, bool $notitle): string
	{
		$mdtitle	= $tab['mdtitle'] ?? '';
		$countLinks = count($tab['sitelinks']);

		$qidE	 = $this->e($qid);
		$mdtitleE = $this->e($mdtitle);
		$mdUrl	= rawurlencode(str_replace(' ', '_', $mdtitle));

		$html = <<<HTML
		<tr>
			<td>$i</td>
			<td><a href='https://wikidata.org/wiki/$qidE'>$qidE</a></td>
			<td>$countLinks</td>
			<td><a href='https://mdwiki.org/wiki/$mdUrl'>$mdtitleE</a></td>
		HTML;

		foreach ($heads as $head) {
			$value = $tab['sitelinks'][$head] ?? '';
			$link = $this->renderSiteLink($head, $value, $notitle);
			$html .= "<td>$link</td>";
		}

		return $html . '</tr>';
	}

	private function renderSiteLink(string $head, string $value, bool $notitle): string
	{
		if ($value === '') {
			return '';
		}

		$url  = 'https://' . $this->e($head) . '.wikipedia.org/wiki/' . rawurlencode(str_replace(' ', '_', $value));
		$text = $notitle ? 'O' : $this->e($value);

		return "<a href='$url'>$text</a>";
	}

	private function e(string $text): string
	{
		return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
	}
}
