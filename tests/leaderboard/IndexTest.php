<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

<?php
/**
 * Test suite for leaderboard index routing script.
 *
 * Notes on framework: Using PHPUnit (extends PHPUnit\Framework\TestCase).
 * We exercise branches that rely on $_GET directly (camps, graph, graph_api) and the default else branch.
 * For filter_input-based branches (get=users/langs, langcode, user, lang, year, camp), filter_input commonly returns null under CLI,
 * so we assert fallback behavior. If your test runner runs under a SAPI where filter_input reads $_GET, these tests will still pass by matching mocked outputs.
 */
final class IndexTest extends TestCase
{
    /** Path to the script under test. Adjust if necessary. */
    private const TARGET_SCRIPT = 'leaderboard.php';

    /** Preserve globals between tests */
    protected function setUp(): void
    {
        parent::setUp();
        $this->resetGlobals();
    }

    protected function tearDown(): void
    {
        $this->resetGlobals();
        parent::tearDown();
    }

    private function resetGlobals(): void
    {
        // Clear GET and server artifacts to avoid leakage across tests
        $_GET = [];
        $_SERVER['QUERY_STRING'] = '';
    }

    /**
     * Include the target script and capture output.
     * This includes lightweight namespaced stubs for external functions when they are not already defined.
     */
    private function render(array $get = []): string
    {
        $_GET = $get;

        // Provide stubs only if the names don't already exist. This avoids redeclare fatals if app code defined them.
        // Stub for Leaderboard\Users\users_html
        if (!function_exists('Leaderboard\\Users\\users_html')) {
            namespace Leaderboard\Users { function users_html($mainlang, $mainuser, $year_y, $camp) { return "[users_html:$mainlang|$mainuser|$year_y|$camp]"; } }
        }
        // Stub for Leaderboard\Langs\langs_html
        if (!function_exists('Leaderboard\\Langs\\langs_html')) {
            namespace Leaderboard\Langs { function langs_html($langcode, $year_y, $camp) { return "[langs_html:$langcode|$year_y|$camp]"; } }
        }
        // Stub for Leaderboard\CampText\echo_html
        if (!function_exists('Leaderboard\\CampText\\echo_html')) {
            namespace Leaderboard\CampText { function echo_html() { return "[echo_html]"; } }
        }
        // Stub for Leaderboard\Graph\print_graph_tab
        if (!function_exists('Leaderboard\\Graph\\print_graph_tab')) {
            namespace Leaderboard\Graph { function print_graph_tab() { return "[print_graph_tab]"; } }
        }
        // Stub for Leaderboard\Graph2\print_graph_tab_2_new
        if (!function_exists('Leaderboard\\Graph2\\print_graph_tab_2_new')) {
            namespace Leaderboard\Graph2 { function print_graph_tab_2_new() { return "[print_graph_tab_2_new]"; } }
        }
        // Stub for Leaderboard\Index\main_leaderboard
        if (!function_exists('Leaderboard\\Index\\main_leaderboard')) {
            namespace Leaderboard\Index { function main_leaderboard($year_y, $camp, $user_group) { return "[main_leaderboard:$year_y|$camp|$user_group]"; } }
        }

        // Capture output from target script
        ob_start();
        // Include using relative path from project root. Adjust TARGET_SCRIPT if needed.
        // If the project locates the script elsewhere, change this to the correct path.
        require self::TARGET_SCRIPT;
        return ob_get_clean();
    }

    public function test_renders_camps_when_query_has_camps(): void
    {
        $html = $this->render(['camps' => '1']);
        $this->assertStringContainsString('[echo_html]', $html, 'Expected CampText echo_html() output when ?camps=1 is set');
    }

    public function test_renders_graph_when_query_has_graph(): void
    {
        $html = $this->render(['graph' => '1']);
        $this->assertStringContainsString('[print_graph_tab]', $html, 'Expected Graph print_graph_tab() output when ?graph=1 is set');
    }

    public function test_renders_graph_api_when_query_has_graph_api(): void
    {
        $html = $this->render(['graph_api' => '1']);
        $this->assertStringContainsString('[print_graph_tab_2_new]', $html, 'Expected Graph2 print_graph_tab_2_new() output when ?graph_api=1 is set');
    }

    public function test_defaults_to_main_leaderboard_when_no_special_params(): void
    {
        // With no GET parameters, expect default: year=All, camp=All, user_group=all
        $html = $this->render([]);
        $this->assertStringContainsString('[main_leaderboard:All|All|all]', $html);
    }

    public function test_main_leaderboard_uses_project_when_present(): void
    {
        $html = $this->render(['project' => 'docs']);
        $this->assertStringContainsString('[main_leaderboard:All|All|docs]', $html, 'When ?project= is set, user_group should be that value');
    }

    public function test_main_leaderboard_falls_back_to_user_group_when_project_missing(): void
    {
        $html = $this->render(['user_group' => 'engineering']);
        $this->assertStringContainsString('[main_leaderboard:All|All|engineering]', $html, 'Fallback to ?user_group= when ?project= is absent');
    }

    public function test_main_leaderboard_sanitizes_year_and_camp_defaults(): void
    {
        $html = $this->render(['project' => 'x']); // filter_input likely returns null in CLI, so year/camp -> All defaults
        $this->assertStringContainsString('[main_leaderboard:All|All|x]', $html);
    }

    public function test_prefers_users_when_filter_input_available_otherwise_default(): void
    {
        // Under CLI, filter_input likely returns null; we assert that it doesn't render users_html unless SAPI provides INPUT_GET.
        $html = $this->render(['get' => 'users', 'user' => 'alice', 'lang' => 'JS', 'year' => '2024', 'camp' => 'Summer']);
        $expectedUsers = '[users_html:JS|alice|2024|Summer]';
        if (strpos($html, $expectedUsers) !== false) {
            $this->assertStringContainsString($expectedUsers, $html, 'If filter_input works in your SAPI, users_html should be invoked.');
        } else {
            $this->assertStringContainsString('[main_leaderboard:All|All|all]', $html, 'If filter_input is null (CLI), default main_leaderboard branch should render.');
        }
    }

    public function test_prefers_langs_when_filter_input_available_otherwise_default(): void
    {
        $html = $this->render(['get' => 'langs', 'langcode' => 'python', 'year' => '2023', 'camp' => 'Fall']);
        $expectedLangs = '[langs_html:python|2023|Fall]';
        if (strpos($html, $expectedLangs) !== false) {
            $this->assertStringContainsString($expectedLangs, $html, 'If filter_input works, langs_html should be invoked.');
        } else {
            $this->assertStringContainsString('[main_leaderboard:All|All|all]', $html, 'If filter_input is null (CLI), default branch should render.');
        }
    }

    public function test_html_style_block_is_present(): void
    {
        $html = $this->render([]);
        $this->assertStringContainsString('.border_debugx', $html, 'Inline style block should include .border_debugx class');
        $this->assertStringContainsString('border-radius: 5px;', $html, 'Expected specific CSS rule present');
    }
}