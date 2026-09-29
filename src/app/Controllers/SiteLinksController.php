<?php

namespace App\SiteLinks;

use App\Logger;
use App\Render\TemplateRenderer;
use function App\Utils\TablesDir\open_td_tables_file;
use App\Settings;

class SiteLinksController
{
	private TemplateRenderer $view;
	public function __construct(?TemplateRenderer $view = null)
	{
		$this->view = $view ?? new TemplateRenderer('Sitelinks');
	}

	/**
	 * Main entry point: read input, load data, prepare the view, render.
	 */
	public function handleRequest(): void
	{
		$params = $this->getRequestParams();
		$data   = $this->loadData();
		$view   = $this->prepareView($params, $data);

		$vars = [
			'params' => $params,
			'view'   => $view,
		];

		$this->view->display('sitelinks', [
			'params' => $params,
			'view'   => $view,
		]);
	}

	// ---------------------------------------------------------------
	// 1) Request input
	// ---------------------------------------------------------------

	private function getRequestParams(): array
	{
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

		Logger::debug("jsons/sitelinks.json: qids_all: " . count($qids));
		Logger::debug("jsons/sitelinks.json: heads_all: " . count($heads));

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

}
