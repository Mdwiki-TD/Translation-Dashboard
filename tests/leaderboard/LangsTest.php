<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// Load stubs first, so if the real project also declares these functions via Composer 'files',
// our definitions come first in include order. If redeclaration occurs, adjust bootstrap to prefer stubs.
require_once __DIR__ . '/../_stubs/Tables/Langs/LangsTables.php';
require_once __DIR__ . '/../_stubs/Leaderboard/Subs/SubLangs/get_langs_tables.php';
require_once __DIR__ . '/../_stubs/Leaderboard/Subs/LeadHelp/make_langs_lead.php';
require_once __DIR__ . '/../_stubs/Leaderboard/SubGraph/graph_data_new.php';
require_once __DIR__ . '/../_stubs/Leaderboard/Subs/FilterForm/lead_row.php';

/**
 * Helper to load the SUT (langs_html) by scanning the repository for its definition.
 * We avoid hardcoding the path to remain compatible with the project's structure.
 */
function __load_langs_sut() : void {
    if (function_exists('\\Leaderboard\\Langs\\langs_html')) {
        return;
    }
    $root = dirname(__DIR__, 2);
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        if ($file->getExtension() !== 'php') { continue; }
        $path = $file->getPathname();
        $contents = @file_get_contents($path);
        if ($contents === false) { continue; }
        if (strpos($contents, 'namespace Leaderboard\\Langs;') !== false &&
            preg_match('/function\\s+langs_html\\s*\\(/', $contents)) {
            require_once $path;
            if (function_exists('\\Leaderboard\\Langs\\langs_html')) {
                return;
            }
        }
    }
    throw new RuntimeException('Could not locate the SUT defining Leaderboard\\Langs\\langs_html');
}

final class LangsTest extends TestCase
{
    protected function setUp(): void
    {
        // Default server name; can be overridden per test
        $_SERVER['SERVER_NAME'] = 'example.org';
        // Clear test flags
        unset($_REQUEST['test'], $_COOKIE['test'], $GLOBALS['__stub_get_langs_tables'], $GLOBALS['__stub_graph_data_new']);

        // Default language map
        \Tables\Langs\LangsTables::$L_code_to_lang_name = [
            'en' => 'English',
            'es' => 'Spanish',
            'pt-br' => 'Portuguese (Brazil)',
        ];

        __load_langs_sut();
    }

    public function test_renders_header_with_decoded_language_and_code_without_cat_link_when_not_localhost(): void
    {
        $html = \Leaderboard\Langs\langs_html('pt-br', 2024, '');
        $this->assertStringContainsString("<h4 class='text-center'>Language: Portuguese (Brazil) (pt-br)", $html);
        $this->assertStringNotContainsString('wikipedia.org/wiki/Category:Translated_from_MDWiki', $html, 'Cat link should not appear by default');
    }

    public function test_mainlang_is_rawurldecoded_and_underscores_converted_to_spaces(): void
    {
        // Given an encoded language with underscores in code
        $input = rawurlencode('zh_classical'); // becomes zh_classical (underscore preserved by encoding/decoding)
        // Not present in map to exercise fallback to original mainlang for $man
        \Tables\Langs\LangsTables::$L_code_to_lang_name = [];
        $html = \Leaderboard\Langs\langs_html($input, 2025, '');
        // After decoding and replace '_' -> ' ', mainlang should be 'zh classical'
        $this->assertStringContainsString('(zh classical)', $html);
        // Since map is empty, $langname should fallback to mainlang
        $this->assertStringContainsString('Language: zh classical (zh classical)', $html);
    }

    public function test_includes_cat_link_when_localhost(): void
    {
        $_SERVER['SERVER_NAME'] = 'localhost';
        $html = \Leaderboard\Langs\langs_html('es', 2023, 'campX');
        $this->assertStringContainsString('href="http://es.wikipedia.org/wiki/Category:Translated_from_MDWiki"', $html);
    }

    public function test_includes_cat_link_when_test_request_flag_present(): void
    {
        $_REQUEST['test'] = '1';
        $html = \Leaderboard\Langs\langs_html('en', 2022, '');
        $this->assertStringContainsString('href="http://en.wikipedia.org/wiki/Category:Translated_from_MDWiki"', $html);
    }

    public function test_graph_and_filter_data_are_embedded_in_lead_row_attributes(): void
    {
        // Inject custom graph data to assert pass-through
        $GLOBALS['__stub_graph_data_new'] = ['points' => 99, 'meta' => ['k' => 'v']];
        $year = 2021; $camp = 'C1';

        $html = \Leaderboard\Langs\langs_html('en', $year, $camp);

        // lead_row stub places JSON in data-filter and data-graph attributes
        $this->assertMatchesRegularExpression('/data-scope=\'lang\'/', $html);
        $this->assertMatchesRegularExpression('/data-filter=\'.*"year":\s*2021.*"camp":\s*"C1".*\'/', $html);
        $this->assertStringContainsString('"points":99', $html);
    }

    public function test_main_and_pending_tables_render_with_correct_types_and_rows(): void
    {
        // Use default stub data: 2 in dd, 1 in dd_Pending
        $html = \Leaderboard\Langs\langs_html('en', 2020, '');

        // Main section should include translations table with two rows
        $this->assertStringContainsString("table data-type='translations' data-lang='en'", $html);
        $this->assertSame(2, substr_count($html, "data-type='translations'"));
        $this->assertStringContainsString('<td>A</td><td>100</td>', $html);
        $this->assertStringContainsString('<td>B</td><td>50</td>', $html);

        // Pending block presence and table
        $this->assertStringContainsString("<h2 class='text-center'>Translations in process</h2>", $html);
        $this->assertStringContainsString("table data-type='pending' data-lang='en'", $html);
        $this->assertStringContainsString('<td>C</td><td>0</td>', $html);
    }

    public function test_handles_empty_datasets_gracefully(): void
    {
        // Inject empty data
        $GLOBALS['__stub_get_langs_tables'] = function($_lang, $_year) {
            return ['dd' => [], 'dd_Pending' => [], 'table_of_views' => []];
        };
        $html = \Leaderboard\Langs\langs_html('en', 2000, '');

        // Should still render scaffolding without rows
        $this->assertStringContainsString("table data-type='translations' data-lang='en'", $html);
        $this->assertStringContainsString("table data-type='pending' data-lang='en'", $html);
        // No <tr> rows
        $this->assertSame(0, substr_count($html, '<tr>'));
    }

    public function test_html_structure_wrapped_in_cards_and_body_containers(): void
    {
        $html = \Leaderboard\Langs\langs_html('en', 2010, '');
        $this->assertStringContainsString("<div class='card mt-1'>", $html);
        $this->assertStringContainsString("<div class='card-body p-1'>", $html);
        $this->assertStringContainsString("<div class='card'>", $html);
        $this->assertStringContainsString("style='padding:5px 0px 5px 5px;'", $html);
    }
}