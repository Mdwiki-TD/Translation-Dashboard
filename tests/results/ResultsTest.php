<?php
declare(strict_types=1);

/**
 * Test suite for Results\Results module.
 *
 * Testing library and framework: PHPUnit (extends PHPUnit\Framework\TestCase).
 *
 * Strategy:
 * - Provide namespaced stubs for external dependencies referenced by the script.
 * - Provide a namespaced filter_input() stub to make CLI-driven tests deterministic.
 * - Capture output via output buffering while including the target file.
 * - Validate behavior across happy paths, edge cases, and failure-like conditions.
 *
 * Important: These tests expect to include the file that declares namespace Results\Results.
 * If path resolution fails, adjust RESULTS_FILE under the test configuration block below.
 */

/////////////////////////////
// Test configuration
/////////////////////////////

/**
 * Update this path if the file under test moves.
 * The repository scan in the planning step should have supplied the proper path.
 * For robustness, we try a few common locations; the first that exists is used.
 */
const RESULTS_CANDIDATE_PATHS = [
    // Common src/module placements (adjust/add if needed)
    'src/Results/Results.php',
    'src/Results/Results/Results.php',
    'app/Results/Results.php',
    'modules/Results/Results.php',
    'public/Results/Results.php',
    // Fallback: discovered tests/files may live alongside include.php
    'Results/Results.php',
];

function __results_test_resolve_target_file(): string {
    foreach (RESULTS_CANDIDATE_PATHS as $p) {
        $abs = \realpath(\dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . $p);
        if ($abs && \is_file($abs)) {
            return $abs;
        }
    }
    // As a last resort, try scanning relative to repo root at runtime with a lightweight directory scan.
    $root = \dirname(__DIR__, 2);
    $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS));
    foreach ($rii as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            // Quick scan for the namespace declaration to pinpoint the file
            $contents = @file_get_contents($file->getPathname());
            if ($contents !== false && preg_match('/^\\s*namespace\\s+Results\\\\\\\\Results\\b/m', $contents)) {
                return $file->getPathname();
            }
        }
    }
    // If still not found, we fail explicitly with guidance.
    throw new \RuntimeException("Unable to locate file under namespace Results\\Results. Update RESULTS_CANDIDATE_PATHS.");
}

/////////////////////////////
// Stubs for external deps
/////////////////////////////

namespace Tables\SqlTables {
    if (!class_exists(TablesSql::class)) {
        class TablesSql {
            public static $s_settings = [];
            public static $s_camp_input_depth = [];
        }
    }
}

namespace Actions\LoadRequest {
    if (!function_exists(__NAMESPACE__ . '\\load_request')) {
        function load_request() {
            return $GLOBALS['__RESULTS_TEST__load_request_return'] ?? [];
        }
    }
}

namespace Results\GetResults {
    if (!function_exists(__NAMESPACE__ . '\\get_results')) {
        function get_results($cat, $camp, $depth, $code) {
            $GLOBALS['__RESULTS_TEST__get_results_args'] = compact('cat','camp','depth','code');
            return $GLOBALS['__RESULTS_TEST__get_results_return'] ?? [
                'inprocess' => [],
                'missing'   => [],
                'ix'        => '',
                'exists'    => [],
            ];
        }
    }
}

namespace Results\ResultsTable {
    if (!function_exists(__NAMESPACE__ . '\\make_results_table')) {
        function make_results_table($list, $code, $cat, $camp, $tra_type, $translation_button, $inprocess = false) {
            $GLOBALS['__RESULTS_TEST__last_table_call'][] = compact('list','code','cat','camp','tra_type','translation_button','inprocess');
            return $inprocess ? '<TABLE_INPROCESS />' : '<TABLE_MISSING />';
        }
    }
}

namespace Results\ResultsTableExists {
    if (!function_exists(__NAMESPACE__ . '\\make_results_table_exists')) {
        function make_results_table_exists($exists, $code, $cat, $camp) {
            $GLOBALS['__RESULTS_TEST__last_exists_call'] = compact('exists','code','cat','camp');
            return '<TABLE_EXISTS />';
        }
    }
}

namespace Results\Results {
    // Emulate filter_input within CLI for INPUT_GET so code under test can be driven by $_GET.
    if (!function_exists(__NAMESPACE__ . '\\filter_input')) {
        function filter_input($type, $var_name, $filter = FILTER_DEFAULT, $options = []) {
            if ($type !== INPUT_GET) {
                return null;
            }
            $value = $_GET[$var_name] ?? null;
            switch ($filter) {
                case FILTER_VALIDATE_BOOL:
                    $ret = \filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
                    return $ret ?? false;
                case FILTER_VALIDATE_INT:
                    return \filter_var($value, FILTER_VALIDATE_INT, $options);
                case FILTER_SANITIZE_FULL_SPECIAL_CHARS:
                    return \filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                default:
                    return $value;
            }
        }
    }
}

/////////////////////////////
// The actual tests
/////////////////////////////

namespace Tests\Results {

use PHPUnit\Framework\TestCase;

final class ResultsTest extends TestCase
{
    private string $file;

    protected function setUp(): void
    {
        // Resolve target file once per test
        $this->file = \__results_test_resolve_target_file();

        // Reset globals and stubs state
        $GLOBALS['__RESULTS_TEST__last_table_call'] = [];
        $GLOBALS['__RESULTS_TEST__last_exists_call'] = null;
        $GLOBALS['__RESULTS_TEST__get_results_args'] = null;
        $GLOBALS['__RESULTS_TEST__get_results_return'] = null;
        $GLOBALS['__RESULTS_TEST__load_request_return'] = null;
        $GLOBALS['user_in_coord'] = false;
        $_GET = [];

        // Default settings
        \Tables\SqlTables\TablesSql::$s_settings = [
            'translation_button_in_progress_table' => ['value' => '0'],
        ];
        \Tables\SqlTables\TablesSql::$s_camp_input_depth = [];
    }

    public function test_card_result_renders_expected_structure_and_content(): void
    {
        // Include the file to load function definitions (suppress output via buffering)
        \ob_start();
        include $this->file;
        \ob_end_clean();

        $html = \Results\Results\card_result('My Title', '<p>Body</p>', '<span id="t2">T2</span>');

        $this->assertStringContainsString("<div class='card'>", $html);
        $this->assertStringContainsString('<div class="card-header">', $html);
        $this->assertStringContainsString('My Title', $html);
        $this->assertStringContainsString('<p>Body</p>', $html);
        $this->assertStringContainsString('<span id="t2">T2</span>', $html);
        $this->assertStringContainsString("card-body1 card2", $html);
    }

    public function test_noop_output_when_doit_is_false_or_code_lang_missing(): void
    {
        // Arrange: even if GET says doit=1, empty code_lang_name forces $doit=false
        $_GET['doit'] = '1';
        $GLOBALS['__RESULTS_TEST__load_request_return'] = [
            'code' => 'C',
            'camp' => 'cmp',
            'cat'  => 'ct',
            'code_lang_name' => '', // empty -> forces $doit=false
        ];

        // Act
        \ob_start();
        include $this->file;
        $out = \ob_get_clean();

        // Assert: container is emitted, but no cards/table content
        $this->assertStringStartsWith("<div class='container-fluid'>", $out);
        $this->assertStringEndsWith("</div>", $out);
        $this->assertStringNotContainsString("class='card'", $out, 'No cards should render when $doit is false');
        $this->assertEmpty($GLOBALS['__RESULTS_TEST__last_table_call'], 'No table calls when $doit is false');
    }

    public function test_renders_missing_and_inprocess_when_doit_true(): void
    {
        // Arrange
        $_GET['doit'] = 'true';
        $_GET['type'] = '<b>x</b>'; // will be sanitized
        $_GET['test'] = '1';        // triggers debug line
        \Tables\SqlTables\TablesSql::$s_camp_input_depth = ['campA' => 7];
        \Tables\SqlTables\TablesSql::$s_settings['translation_button_in_progress_table']['value'] = '0'; // stays '0'

        $GLOBALS['__RESULTS_TEST__load_request_return'] = [
            'code' => 'CODE1',
            'camp' => 'campA',
            'cat'  => 'CAT1',
            'code_lang_name' => 'en',
        ];

        $GLOBALS['__RESULTS_TEST__get_results_return'] = [
            'missing'   => [['id' => 1], ['id' => 2], ['id' => 3]],
            'inprocess' => [['id' => 10]],
            'ix'        => 'IX-123',
            'exists'    => [1,2,3,4,5], // not enough to trigger Exists card
        ];

        // Act
        \ob_start();
        include $this->file;
        $out = \ob_get_clean();

        // Assert debug echo and results count
        $this->assertStringContainsString('code:CODE1<br>code_lang_name:en<br>', $out);
        $this->assertStringContainsString('Results: (3)', $out);
        $this->assertStringContainsString('test:', $out);
        // Assert hint/comment with ix
        $this->assertStringContainsString('<!-- IX-123 -->', $out);

        // Assert missing table rendered and in-process table rendered
        $this->assertStringContainsString('<TABLE_MISSING />', $out);
        $this->assertStringContainsString('<TABLE_INPROCESS />', $out);

        // Validate arguments passed into stubs
        $calls = $GLOBALS['__RESULTS_TEST__last_table_call'];
        $this->assertNotEmpty($calls);
        // First call: missing items
        $this->assertSame('CODE1', $calls[0]['code']);
        $this->assertSame('CAT1',  $calls[0]['cat']);
        $this->assertSame('campA', $calls[0]['camp']);
        $this->assertFalse($calls[0]['inprocess']);

        // tra_type is sanitized
        $expectedType = \filter_var('<b>x</b>', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $this->assertSame($expectedType, $calls[0]['tra_type']);

        // translation_button remains '0' because setting value was '0'
        $this->assertSame('0', $calls[0]['translation_button']);

        // Second call: in-process with inprocess flag true
        $this->assertTrue($calls[1]['inprocess']);

        // Depth override: ensure get_results received depth 7 from camp mapping
        $args = $GLOBALS['__RESULTS_TEST__get_results_args'];
        $this->assertSame(7, $args['depth']);
    }

    public function test_translation_button_enabled_only_for_coord_users_when_setting_is_one(): void
    {
        // Arrange
        $_GET['doit'] = '1';
        $GLOBALS['user_in_coord'] = true;
        \Tables\SqlTables\TablesSql::$s_settings['translation_button_in_progress_table']['value'] = '1';

        $GLOBALS['__RESULTS_TEST__load_request_return'] = [
            'code' => 'C2',
            'camp' => 'CAMP2',
            'cat'  => 'CAT2',
            'code_lang_name' => 'ar',
        ];

        $GLOBALS['__RESULTS_TEST__get_results_return'] = [
            'missing'   => [['id'=>1]],
            'inprocess' => [],
            'ix'        => 'IX-0',
            'exists'    => [],
        ];

        // Act
        \ob_start();
        include $this->file;
        $out = \ob_get_clean();

        // Assert: one table rendered, and translation_button was '1'
        $calls = $GLOBALS['__RESULTS_TEST__last_table_call'];
        $this->assertCount(1, $calls);
        $this->assertSame('1', $calls[0]['translation_button']);
        $this->assertStringContainsString('<TABLE_MISSING />', $out);
        $this->assertStringNotContainsString('<TABLE_INPROCESS />', $out);
    }

    public function test_exists_table_renders_only_above_threshold(): void
    {
        // Case 1: exactly 5000 -> should NOT render exists
        $_GET['doit'] = '1';
        $GLOBALS['__RESULTS_TEST__load_request_return'] = [
            'code' => 'C3',
            'camp' => 'CAMP3',
            'cat'  => 'CAT3',
            'code_lang_name' => 'fr',
        ];
        $GLOBALS['__RESULTS_TEST__get_results_return'] = [
            'missing'   => [],
            'inprocess' => [],
            'ix'        => 'IX-A',
            'exists'    => \array_fill(0, 5000, 1),
        ];

        \ob_start();
        include $this->file;
        $out5000 = \ob_get_clean();

        $this->assertStringNotContainsString('<TABLE_EXISTS />', $out5000);

        // Case 2: 5001 -> should render exists
        $_GET['doit'] = '1';
        $GLOBALS['__RESULTS_TEST__get_results_return']['exists'] = \array_fill(0, 5001, 1);

        \ob_start();
        include $this->file;
        $out5001 = \ob_get_clean();

        $this->assertStringContainsString('<TABLE_EXISTS />', $out5001);
    }
}
}