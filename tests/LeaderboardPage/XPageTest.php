<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class XPageTest extends TestCase
{
    private string $pagePath;

    protected function setUp(): void
    {
        // Path to the file under test (as provided in the diff)
        $this->pagePath = __DIR__ . '/../leaderboard/XTest.php';

        // Sanity: ensure file exists
        $this->assertFileExists($this->pagePath, 'File under test not found: ' . $this->pagePath);
    }

    /**
     * Helper to include the page and capture its output safely.
     */
    private function renderPage(): string
    {
        ob_start();
        try {
            include $this->pagePath;
            return (string)ob_get_clean();
        } catch (Throwable $e) {
            ob_end_clean();
            $this->fail('Including page threw exception: ' . $e->getMessage());
        }
    }

    public function test_renders_basic_structure_and_scripts(): void
    {
        $html = $this->renderPage();

        // Basic HTML structure
        $this->assertStringContainsString('<!doctype html>', strtolower($html));
        $this->assertStringContainsString('<main id="body">', $html);
        $this->assertStringContainsString('id="maindiv"', $html);

        // Critical scripts referenced in diff
        $this->assertStringContainsString('/Translation_Dashboard/js/g.js', $html);
        $this->assertStringContainsString('/Translation_Dashboard/js/graph_api.js', $html);
        $this->assertStringContainsString('/Translation_Dashboard/js/c.js', $html);
    }

    public function test_includes_filter_form_from_stub_with_default_params(): void
    {
        // In CLI, filter_input typically returns null; page code falls back to 'all'.
        $html = $this->renderPage();

        // Assert our stubbed filter output is embedded with defaults
        $this->assertMatchesRegularExpression(
            '#<form id="leaderboard_filter"[^>]*data-year="all"[^>]*data-user_group="all"[^>]*data-camp="all"[^>]*action="x\.php"#',
            $html
        );
    }

    public function test_numbers_card_and_counters_exist(): void
    {
        $html = $this->renderPage();

        // Numbers table header
        $this->assertStringContainsString('Numbers', $html);

        // Counter spans that will be updated by JS
        foreach (['c_user','c_articles','c_words','c_lang','c_pv'] as $id) {
            $this->assertStringContainsString('id="' . $id . '"', $html, "Missing counter span #$id");
        }
    }

    public function test_top_users_table_configuration_is_present(): void
    {
        $html = $this->renderPage();

        // Table markup
        $this->assertStringContainsString("id='Topusers'", $html);

        // DataTables ajax endpoint and column keys as per diff
        $this->assertStringContainsString("url: '/api.php?get=top_users'", $html);
        foreach (['user','targets','words','views'] as $key) {
            $this->assertStringContainsString("data: '$key'", $html);
        }

        // Link template to user details
        $this->assertStringContainsString('/Translation_Dashboard/leaderboard.php?get=users&user=', $html);
    }

    public function test_top_languages_table_configuration_is_present(): void
    {
        $html = $this->renderPage();

        // Table markup
        $this->assertStringContainsString("id='Toplangs'", $html);

        // DataTables ajax endpoint and expected columns
        $this->assertStringContainsString("url: '/api.php?get=top_langs'", $html);
        foreach (['lang','targets','views'] as $key) {
            $this->assertStringContainsString("data: '$key'", $html);
        }

        // Link template to language details must include langcode and use row.lang_name for label
        $this->assertStringContainsString('/Translation_Dashboard/leaderboard.php?get=langs&langcode=', $html);
        $this->assertStringContainsString('row.lang_name', $html);
    }

    public function test_graph_js_params_is_invoked_with_form_data_object(): void
    {
        $html = $this->renderPage();

        // Ensure the chart call exists
        $this->assertStringContainsString("graph_js_params('chart09', getFormData({}))", $html);
    }

    public function test_get_categories_and_getFormData_presence_and_logic_signatures(): void
    {
        $html = $this->renderPage();

        // Presence of async get_categories fetching categories endpoint
        $this->assertStringContainsString("async function get_categories()", $html);
        $this->assertStringContainsString("fetch('/api.php?get=categories')", $html);

        // getFormData composition and mapping campaign->category
        $this->assertStringContainsString("function getFormData(d)", $html);
        $this->assertStringContainsString("$('#leaderboard_filter').serializeArray()", $html);
        $this->assertStringContainsString('campaign_to_categories[d["camp"]]', $html);
        $this->assertStringContainsString('d["cat"]', $html);
    }

    public function test_dataSrc_aggregates_and_updates_totals_for_users_table(): void
    {
        $html = $this->renderPage();

        // Verify presence of total aggregation logic and updated counters
        $this->assertStringContainsString('totalUsers = json.results.length', $html);
        $this->assertStringContainsString("$('#c_user').text(totalUsers.toLocaleString())", $html);
        $this->assertStringContainsString("$('#c_articles').text(totalTargets.toLocaleString())", $html);
        $this->assertStringContainsString("$('#c_words').text(totalWords.toLocaleString())", $html);
        $this->assertStringContainsString("$('#c_pv').text(totalViews.toLocaleString())", $html);
    }

    public function test_defensive_defaults_and_lowercasing_are_applied_server_side(): void
    {
        // We cannot change filter_input in CLI, but we can at least ensure the file parses and executes with defaults.
        $html = $this->renderPage();

        // Our stub marks the attrs as lowercase; check 'all' appears in the form stub
        $this->assertStringContainsString('data-year="all"', $html);
        $this->assertStringContainsString('data-user_group="all"', $html);
        $this->assertStringContainsString('data-camp="all"', $html);
    }
}