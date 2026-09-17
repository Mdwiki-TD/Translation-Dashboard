<?php

namespace Results\GetResults2026;

use Results\GetResults2026\Data\ResultsFetcher;
use Results\GetResults2026\Helpers\CardRenderer;
use Results\GetResults2026\Helpers\TranslateTypeLoader;
use Results\GetResults2026\Tables\MissingTable;
use Results\GetResults2026\Tables\ExistsTable;
use Results\GetResults2026\Tables\InProcessTable;

use function SQLorAPI\GetDataTab\get_td_or_sql_full_translators;
use function SQLorAPI\GetDataTab\get_td_or_sql_titles_infos;
use function SQLorAPI\GetDataTab\get_endpoint;

/**
 * Main entry point for the 2026 results module.
 */
class ResultsLoader
{
    /**
     * Load and render the complete results page.
     */
    public function load(array $data): string
    {
        $camp         = $data['camp'] ?? '';
        $code         = $data['code'] ?? '';
        $cat          = $data['cat'] ?? '';
        $showExists   = (bool)($data['show_exists'] ?? false);
        $globalUser   = $data['global_username'] ?? null;
        $inProgressBtn = (bool)($data['in_progress_translation_button'] ?? false);
        $traType      = $data['tra_type'] ?? 'lead';
        $userCoord    = (bool)($data['user_coord'] ?? false);
        $test         = !empty($data['test']);

        // Full translator check
        $fullTranslators = get_td_or_sql_full_translators();
        $fullTranslators = array_column($fullTranslators, 'is_active', 'user');
        $fullTrUser = ($fullTranslators[$globalUser] ?? 0) == 1;

        // Fetch data
        $fetcher = new ResultsFetcher($test);
        $results = $fetcher->get($cat, $code);

        // Helper data
        $titlesInfos     = array_column(get_td_or_sql_titles_infos(), null, 'title');
        $noLeadTranslates = TranslateTypeLoader::load('no');
        $fullTranslates   = TranslateTypeLoader::load('full');
        $endpoint         = get_endpoint();

        $html = '';

        if ($test) {
            $html .= "code:{$code}<br>code_lang_name:" . ($data['code_lang_name'] ?? '') . "<br>";
        }

        // ----- Missing table -----
        $missingTable = new MissingTable(
            $code,
            $cat,
            $camp,
            $traType,
            $fullTrUser,
            $globalUser,
            $noLeadTranslates,
            $fullTranslates
        );

        $missingHtml = $missingTable->render($results['missing']);
        $resLine = ' Results: (' . count($results['missing']) . ')';
        if ($test) {
            $resLine .= ' test:';
        }

        $html .= CardRenderer::render($resLine, $missingHtml, $results['ix']);

        // ----- In-process table -----
        $lenInProcess = count($results['inprocess']);
        if ($lenInProcess > 0) {
            $inProcessTable = new InProcessTable(
                $code,
                $cat,
                $camp,
                $inProgressBtn,
                $fullTrUser,
                $globalUser,
                $titlesInfos,
                $endpoint,
                $userCoord
            );

            $html .= CardRenderer::render(
                "In process: ({$lenInProcess})",
                $inProcessTable->render($results['inprocess'])
            );
        }

        // ----- Exists table -----
        $lenExists = count($results['exists']);
        if ($lenExists > 1 && $showExists) {
            $existsTable = new ExistsTable(
                $code,
                $cat,
                $camp,
                $globalUser,
                $userCoord,
                $endpoint
            );

            $html .= CardRenderer::render(
                "Exists: ({$lenExists})",
                $existsTable->render($results['exists'])
            );
        }

        return $html;
    }
}
