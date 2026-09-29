<?php

namespace App\Missing;

use App\Render\TemplateRenderer;
use function App\SQLorAPI\staticsByCategory;
use function App\SQLorAPI\countCategoryMembers;
use function App\SQLorAPI\getLangs;

class MissingController
{
	private TemplateRenderer $view;

	private const CATEGORY = 'RTT';

	public function __construct(?TemplateRenderer $view = null)
	{
		$this->view = $view ?? new TemplateRenderer('Missing');
	}

	/**
	 * Main entry point: load data, build rows, render the template.
	 */
	public function handleRequest(): void
	{
		$totalPages = $this->getCategoryLength(self::CATEGORY);
		$rows	   = $this->buildRows(self::CATEGORY, $totalPages);

		$this->view->display('missing', [
			'category'   => self::CATEGORY,
			'totalPages' => $totalPages,
			'rows'       => $rows,
		]);
	}

	// ---------------------------------------------------------------
	// 1) Data loading
	// ---------------------------------------------------------------

	/**
	 * Number of pages (members) in the category.
	 */
	private function getCategoryLength(string $category): int
	{
		$length = 0;

		foreach (countCategoryMembers($category) as $row) {
			$length = (int)($row['members'] ?? 0);
		}

		return $length;
	}

	// ---------------------------------------------------------------
	// 2) Row building
	// ---------------------------------------------------------------

	/**
	 * Build the table rows: one per language, with existing/missing counts.
	 */
	private function buildRows(string $category, int $totalPages): array
	{
		$stats	 = staticsByCategory($category);
		$langsData = getLangs();

		$rows = [];
		$num  = 0;

		foreach ($stats as $row) {
			// Example row:
			// { "language_code": "ar", "autonym": "العربية", "language_name": "Arabic",
			//   "available_title_count": 3132, "missing_title_count": 4, "total": 3136 }
			$langCode = $row['language_code'] ?? '';
			$langData = $langsData[$langCode] ?? [];

			$autonym  = $langData['autonym'] ?? '';
			$langName = $langData['name'] ?? '';

			$exists  = (int)($row['available_title_count'] ?? 0);
			$missing = $totalPages - $exists;

			$rows[] = [
				'num'	  	=> ++$num,
				'code'	 	=> $langCode,
				'name'	 	=> $langName !== '' ? $langName : '! langname',
				'autonym'   => $autonym !== '' ? $autonym : '! autonym',
				'exists'    => $exists,
				'missing'   => $missing,
			];
		}

		return $rows;
	}
}
