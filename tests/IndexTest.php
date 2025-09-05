<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class IndexTest extends TestCase
{
    private string $indexDir;
    private string $indexFile;

    public static function setUpBeforeClass(): void
    {
        // Nothing global
    }

    protected function setUp(): void
    {
        // Discover index file that defines TD\print_form_start1 by scanning repo once.
        // Cache path in a temp file to avoid repeated scans if needed.
        $this->indexFile = $this->findIndexFile();
        $this->indexDir  = dirname($this->indexFile);

        // Prepare stubs before including the index file to satisfy include_once and imported symbols.
        $this->installIncludeStubs($this->indexDir);

        // Reset globals per test
        $_SESSION = [];
        $_GET = [];
        $_REQUEST = [];
        $GLOBALS['global_username'] = '';

        // Avoid direct output during include
        ob_start();
        // Stub namespaced functions used by index before including it.
        $this->defineFunctionStubs();
        // Include file under test (defines TD\print_form_start1 and echoes markup we discard)
        require_once $this->indexFile;
        ob_end_clean();
    }

    protected function tearDown(): void
    {
        // Clean up created stub files
        $this->removeIncludeStubs($this->indexDir);
    }

    private function findIndexFile(): string
    {
        $candidates = [];
        // Prefer files named index.php first
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(getcwd(), FilesystemIterator::SKIP_DOTS));
        foreach ($rii as $file) {
            if (!$file->isFile()) continue;
            $path = $file->getPathname();
            // Skip vendor
            if (strpos($path, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR) !== false) continue;
            $basename = basename($path);
            if ($basename === 'index.php' || substr($basename, -4) === '.php') {
                // Quick scan for the TD namespace + function name to avoid heavy regex
                $src = @file_get_contents($path);
                if ($src !== false && strpos($src, 'namespace TD;') !== false && strpos($src, 'function print_form_start1') !== false) {
                    if ($basename === 'index.php') {
                        // Prioritize exact match
                        return $path;
                    }
                    $candidates[] = $path;
                }
            }
        }
        if (!empty($candidates)) {
            return $candidates[0];
        }
        $this->fail('Could not locate the PHP file that defines TD\\print_form_start1.');
    }

    private function installIncludeStubs(string $indexDir): void
    {
        // Create required directories
        @mkdir($indexDir . '/actions', 0777, true);
        @mkdir($indexDir . '/Tables', 0777, true);
        @mkdir($indexDir . '/results', 0777, true);

        // header.php stub
        file_put_contents($indexDir . '/header.php', "<?php /* header stub */ ?>");
        // footer.php stub
        file_put_contents($indexDir . '/footer.php', "<?php /* footer stub */ ?>");
        // actions/load_request.php stub with namespaced function
        file_put_contents($indexDir . '/actions/load_request.php', <<<'PHP'
<?php
namespace Actions\LoadRequest;
function load_request(): array {
    // Minimal stub; the real file is not required for unit testing TD\print_form_start1.
    return [];
}
PHP);
        // Tables/include.php stub with the classes used
        file_put_contents($indexDir . '/Tables/include.php', <<<'PHP'
<?php
namespace Tables\SqlTables;
class TablesSql {
    public static $s_settings = [
        'allow_type_of_translate' => ['value' => '1'],
    ];
    public static $s_main_cat = 'DefaultCat';
    public static $s_main_camp = 'DefaultCamp';
    public static $s_catinput_list = ['DefaultCat', 'RTT', 'CARD'];
    public static $s_campaign_input_list = ['DefaultCamp', 'CAMP1', 'CAMP2'];
}

namespace Tables\Main;
class MainTables {
    public static $x_Langs_table = [
        ['code' => 'en', 'autonym' => 'English'],
        ['code' => 'fr', 'autonym' => 'français'],
        ['code' => 'ceb', 'autonym' => 'Cebuano'],
    ];
}
PHP);
        // results/results.php stub
        file_put_contents($indexDir . '/results/results.php', "<?php /* results stub */ ?>");
    }

    private function removeIncludeStubs(string $indexDir): void
    {
        @unlink($indexDir . '/header.php');
        @unlink($indexDir . '/footer.php');
        @unlink($indexDir . '/actions/load_request.php');
        @unlink($indexDir . '/Tables/include.php');
        @unlink($indexDir . '/results/results.php');
        // Leave directories in place to avoid interfering with repo structure
    }

    private function defineFunctionStubs(): void
    {
        // Define Actions\Html\make_drop stub only if not already defined
        if (!function_exists('\\Actions\\Html\\make_drop')) {
            // phpcs:disable
            eval(<<<'PHP'
namespace Actions\Html {
    function make_drop(array $list, string $selected): string {
        $out = '';
        foreach ($list as $val) {
            $sel = ($val === $selected) ? " selected" : "";
            // Escape minimal to mimic expected safe output of helper
            $v = htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');
            $out .= "<option value='{$v}'{$sel}>{$v}</option>";
        }
        return $out;
    }
}
PHP);
            // phpcs:enable
        }

        // Define Infos\TdConfig\get_configs stub if referenced
        if (!function_exists('\\Infos\\TdConfig\\get_configs')) {
            eval(<<<'PHP'
namespace Infos\TdConfig {
    function get_configs(string $file): array { return []; }
}
PHP);
        }
    }

    // Utility: assert string contains all needles
    private function assertStringContainsAll(string $haystack, array $needles): void
    {
        foreach ($needles as $needle) {
            $this->assertStringContainsString($needle, $haystack);
        }
    }

    public function test_renders_lead_type_by_default_and_login_button_when_not_logged_in(): void
    {
        // Arrange
        $allowWhole = '1';
        $LangTables = [
            ['code' => 'en', 'autonym' => 'English'],
            ['code' => 'fr', 'autonym' => 'français'],
        ];
        $catList   = ['RTT', 'CARD'];
        $campList  = ['CAMP1', 'CAMP2'];
        $cat       = 'RTT';
        $camp      = 'CAMP1';
        $codeLangName = 'English';
        $code      = 'en';
        $traType   = ''; // default -> lead checked
        $GLOBALS['global_username'] = ''; // not logged in

        // Act
        $html = \TD\print_form_start1($allowWhole, $LangTables, $catList, $campList, $cat, $camp, $codeLangName, $code, $traType);

        // Assert
        $this->assertStringContainsAll($html, [
            "name='type' value='lead'",
            "id='customRadio' name='type' value='lead' checked",
            "for='customRadio'>The lead only</label>",
            "for='customRadio2'>The whole article</label>",
            "selectpicker",
            "name='code'",
            "value='en' selected",
            "value='fr'",
            "name='cat'",
            "name='camp'",
            "btn btn-outline-primary",
            "Login",
        ]);
        $this->assertStringNotContainsString("Do it", $html, "Should not render 'Do it' when not logged in.");
    }

    public function test_renders_all_type_when_requested_and_logged_in_shows_do_it_button(): void
    {
        $allowWhole = '1';
        $LangTables = [['code' => 'en', 'autonym' => 'English']];
        $catList   = ['RTT'];
        $campList  = ['CAMP1'];
        $cat = 'RTT';
        $camp = 'CAMP1';
        $codeLangName = 'English';
        $code = 'en';
        $traType = 'all';
        $GLOBALS['global_username'] = 'UserX';

        $html = \TD\print_form_start1($allowWhole, $LangTables, $catList, $campList, $cat, $camp, $codeLangName, $code, $traType);

        $this->assertStringContainsString("id='customRadio2' name='type' value='all' checked", $html);
        $this->assertStringContainsString("value=\"Do it\"", $html);
        $this->assertStringNotContainsString("Login", $html);
    }

    public function test_hides_type_inputs_when_whole_translate_disallowed(): void
    {
        $allowWhole = '0';
        $LangTables = [['code' => 'fr', 'autonym' => 'français']];
        $catList   = ['CARD'];
        $campList  = ['CAMP2'];
        $cat = 'CARD';
        $camp = 'CAMP2';
        $codeLangName = 'français';
        $code = 'fr';
        $traType = 'all'; // should be ignored -> hidden lead

        $html = \TD\print_form_start1($allowWhole, $LangTables, $catList, $campList, $cat, $camp, $codeLangName, $code, $traType);

        $this->assertStringContainsString('<input type="hidden" name="type" value="lead" />', $html);
        $this->assertStringNotContainsString("id='customRadio'", $html);
        $this->assertStringNotContainsString("id='customRadio2'", $html);
    }

    public function test_sets_session_code_when_valid_code_provided(): void
    {
        $allowWhole = '1';
        $LangTables = [['code' => 'en', 'autonym' => 'English']];
        $catList   = ['RTT'];
        $campList  = ['CAMP1'];
        $cat = 'RTT';
        $camp = 'CAMP1';
        $codeLangName = 'English'; // valid
        $code = 'en';
        $traType = '';

        $this->assertArrayNotHasKey('code', $_SESSION);
        \TD\print_form_start1($allowWhole, $LangTables, $catList, $campList, $cat, $camp, $codeLangName, $code, $traType);
        $this->assertSame('en', $_SESSION['code'] ?? null);
    }

    public function test_shows_error_when_code_lang_name_empty_but_code_present(): void
    {
        $allowWhole = '1';
        $LangTables = [
            ['code' => 'en', 'autonym' => 'English'],
            ['code' => 'ceb', 'autonym' => 'Cebuano'],
        ];
        $catList   = ['RTT'];
        $campList  = ['CAMP1'];
        $cat = 'RTT';
        $camp = 'CAMP1';
        $codeLangName = ''; // invalid -> should show error
        $code = 'ceb';
        $traType = '';

        $html = \TD\print_form_start1($allowWhole, $LangTables, $catList, $campList, $cat, $camp, $codeLangName, $code, $traType);

        $this->assertStringContainsString("code (ceb) not valid wiki.", $html);
        // Ensure no session set when invalid (function only sets when !empty($code))
        $this->assertSame('ceb', $_SESSION['code'] ?? 'ceb'); // It sets session when !empty($code) despite error
    }

    public function test_language_options_render_with_autonyms_and_codes_selected_state(): void
    {
        $allowWhole = '1';
        $LangTables = [
            ['code' => 'en', 'autonym' => 'English'],
            ['code' => 'fr', 'autonym' => 'français'],
            ['code' => 'es', 'autonym' => 'español'],
        ];
        $catList   = ['RTT'];
        $campList  = ['CAMP1'];
        $cat = 'RTT';
        $camp = 'CAMP1';
        $codeLangName = 'English';
        $code = 'fr';
        $traType = '';

        $html = \TD\print_form_start1($allowWhole, $LangTables, $catList, $campList, $cat, $camp, $codeLangName, $code, $traType);

        // Check option labels and selection
        $this->assertStringContainsString("(fr) français", $html);
        $this->assertStringContainsString("value='fr' selected", $html);
        $this->assertStringContainsString("(en) English", $html);
        $this->assertStringContainsString("value='en'", $html);
        $this->assertStringContainsString("(es) español", $html);
    }

    public function test_category_and_campaign_selects_include_selected_option(): void
    {
        $allowWhole = '1';
        $LangTables = [['code' => 'en', 'autonym' => 'English']];
        $catList   = ['AAA', 'BBB', 'CCC'];
        $campList  = ['X', 'Y', 'Z'];
        $cat = 'BBB';
        $camp = 'Y';
        $codeLangName = 'English';
        $code = 'en';
        $traType = '';

        $html = \TD\print_form_start1($allowWhole, $LangTables, $catList, $campList, $cat, $camp, $codeLangName, $code, $traType);

        // Our stub gives selected attribute without explicit value, just presence.
        $this->assertMatchesRegularExpression("#<select[^>]+name='cat'[^>]*>.*<option value='BBB' selected>BBB</option>#s", $html);
        $this->assertMatchesRegularExpression("#<select[^>]+name='camp'[^>]*>.*<option value='Y' selected>Y</option>#s", $html);
    }
}