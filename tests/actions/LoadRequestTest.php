<?php
declare(strict_types=1);

/*
Testing framework: PHPUnit. This test class extends PHPUnit\Framework\TestCase
and follows the repository's tests/ structure and naming conventions.
*/

namespace Tables\SqlTables {
    // Define stub if the real one isn't autoloaded.
    if (!class_exists('Tables\\SqlTables\\TablesSql')) {
        class TablesSql {
            public static $s_camp_to_cat = [];
            public static $s_cat_to_camp = [];
        }
    }
}

namespace Tables\Langs {
    // Define stub if the real one isn't autoloaded.
    if (!class_exists('Tables\\Langs\\LangsTables')) {
        class LangsTables {
            public static $L_lang_to_code = [];
            public static $L_code_to_lang = [];
        }
    }
}

namespace Tests\Actions {

use PHPUnit\Framework\TestCase;

// Resolve implementation file path at runtime so tests don't hardcode paths.
final class LoadRequestTest extends TestCase
{
    private static string $implFile;

    public static function setUpBeforeClass(): void
    {
        // Try to resolve the file that declares the function
        $candidates = [
            'actions/LoadRequest/load_request.php',
            'actions/LoadRequest/index.php',
            'actions/LoadRequest/LoadRequest.php',
            'src/Actions/LoadRequest/load_request.php',
            'src/Actions/LoadRequest/index.php',
            'app/Actions/LoadRequest/load_request.php',
            'app/Actions/LoadRequest/index.php',
        ];
        foreach ($candidates as $cand) {
            if (is_file($cand)) {
                self::$implFile = $cand;
                break;
            }
        }
        if (!isset(self::$implFile)) {
            // Attempt a lightweight scan as fallback
            $iter = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator('.'));
            foreach ($iter as $file) {
                if ($file->isFile() && substr($file->getFilename(), -4) === '.php') {
                    $path = $file->getPathname();
                    $src = @file_get_contents($path);
                    if ($src !== false
                        && strpos($src, 'namespace Actions\\LoadRequest') !== false
                        && preg_match('/function\\s+load_request\\s*\\(/', $src)) {
                        self::$implFile = $path;
                        break;
                    }
                }
            }
        }
        if (!isset(self::$implFile)) {
            self::markTestSkipped('Could not locate the Actions\\LoadRequest\\load_request implementation file.');
        }

        // Create stubs for sibling includes to avoid E_WARNING if missing.
        $dir = dirname(self::$implFile);
        foreach (['html.php','wiki_api.php','mdwiki_api.php','td_api.php','mdwiki_sql.php'] as $stub) {
            $p = $dir . DIRECTORY_SEPARATOR . $stub;
            if (!is_file($p)) {
                @file_put_contents($p, "<?php // test stub: $stub");
            }
        }
        $apiDir = realpath($dir . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'api_or_sql');
        if ($apiDir && !is_file($apiDir . DIRECTORY_SEPARATOR . 'index.php')) {
            @file_put_contents($apiDir . DIRECTORY_SEPARATOR . 'index.php', "<?php // test stub: api_or_sql/index.php");
        }

        // Require the implementation once for this test suite.
        require_once self::$implFile;
    }

    protected function setUp(): void
    {
        // Reset superglobals and static mappings before each test.
        $_GET = [];
        $_REQUEST = [];
        $_COOKIE = [];

        // Reset mapping arrays to controlled defaults.
        \Tables\SqlTables\TablesSql::$s_camp_to_cat = [];
        \Tables\SqlTables\TablesSql::$s_cat_to_camp = [];
        \Tables\Langs\LangsTables::$L_lang_to_code = [];
        \Tables\Langs\LangsTables::$L_code_to_lang = [];
    }

    private function callLoadRequest(): array
    {
        // FQN of namespaced function
        $fn = '\\Actions\\LoadRequest\\load_request';
        $this->assertTrue(function_exists($fn), 'Function Actions\\LoadRequest\\load_request must exist.');
        return $fn();
    }

    public function testReturnsDefaultsWhenNoQueryParamsProvided(): void
    {
        $result = $this->callLoadRequest();
        $this->assertSame('', $result['code']);
        $this->assertSame('All', $result['cat']);
        $this->assertSame('All', $result['camp']);
        $this->assertSame('', $result['code_lang_name']);
    }

    public function testMapsLanguageNameToCodeAndBack(): void
    {
        \Tables\Langs\LangsTables::$L_lang_to_code = ['English' => 'en'];
        \Tables\Langs\LangsTables::$L_code_to_lang = ['en' => 'English'];

        $_GET['code'] = 'English';
        $out = $this->callLoadRequest();

        $this->assertSame('en', $out['code']);
        $this->assertSame('English', $out['code_lang_name']);
    }

    public function testUndefinedCodeBecomesEmptyString(): void
    {
        $_GET['code'] = 'undefined';
        $out = $this->callLoadRequest();

        $this->assertSame('', $out['code']);
        $this->assertSame('', $out['code_lang_name']);
    }

    public function testCatUndefinedBecomesEmptyAndCampMapsToCat(): void
    {
        \Tables\SqlTables\TablesSql::$s_camp_to_cat = ['CAMP_A' => 'CAT_A'];

        $_GET['cat']  = 'undefined';
        $_GET['camp'] = 'CAMP_A';
        $out = $this->callLoadRequest();

        // After 'cat' becomes empty, mapping from camp should fill cat
        $this->assertSame('CAT_A', $out['cat']);
        $this->assertSame('CAMP_A', $out['camp']);
    }

    public function testCatMapsToCampWhenCampMissing(): void
    {
        \Tables\SqlTables\TablesSql::$s_cat_to_camp = ['Cardiology' => 'CampCardio'];

        $_GET['cat'] = 'Cardiology';
        $out = $this->callLoadRequest();

        $this->assertSame('Cardiology', $out['cat']);
        $this->assertSame('CampCardio', $out['camp']);
    }

    public function testHtmlSpecialCharsAreEscapedInInputs(): void
    {
        \Tables\Langs\LangsTables::$L_lang_to_code = []; // No mapping, keep sanitized value
        $rawCode = '\'"<script>alert(1)</script>';
        $rawCat  = '"Oncology\'';
        $rawCamp = "Camp & Research";

        $_GET['code'] = $rawCode;
        $_GET['cat']  = $rawCat;
        $_GET['camp'] = $rawCamp;

        $out = $this->callLoadRequest();

        $this->assertSame(htmlspecialchars($rawCode, ENT_QUOTES, 'UTF-8'), $out['code']);
        $this->assertSame(htmlspecialchars($rawCat, ENT_QUOTES, 'UTF-8'),  $out['cat']);
        $this->assertSame(htmlspecialchars($rawCamp, ENT_QUOTES, 'UTF-8'), $out['camp']);
    }

    public function testKeepsUnknownLanguageCodeAndEmptyLangName(): void
    {
        \Tables\Langs\LangsTables::$L_lang_to_code = ['French' => 'fr'];
        \Tables\Langs\LangsTables::$L_code_to_lang = ['fr' => 'French'];

        $_GET['code'] = 'xx'; // not mapped; should remain 'xx'
        $out = $this->callLoadRequest();

        $this->assertSame('xx', $out['code']);
        $this->assertSame('', $out['code_lang_name']);
    }

    public function testWhenCampProvidedAndCatEmptyDerivesCatFromCampOnlyIfMappingExists(): void
    {
        \Tables\SqlTables\TablesSql::$s_camp_to_cat = ['CAMP_X' => 'CAT_X'];

        $_GET['cat']  = '';        // empty
        $_GET['camp'] = 'CAMP_Y';  // no mapping
        $out = $this->callLoadRequest();
        $this->assertSame('', $out['cat']);
        $this->assertSame('CAMP_Y', $out['camp']);

        // Now with mapping present
        $_GET['camp'] = 'CAMP_X';
        $out2 = $this->callLoadRequest();
        $this->assertSame('CAT_X', $out2['cat']);
        $this->assertSame('CAMP_X', $out2['camp']);
    }

    public function testWhenCatProvidedAndCampEmptyDerivesCampFromCatOnlyIfMappingExists(): void
    {
        \Tables\SqlTables\TablesSql::$s_cat_to_camp = ['CAT_Z' => 'CAMP_Z'];

        $_GET['cat']  = 'CAT_W';  // no mapping
        $_GET['camp'] = '';       // empty
        $out = $this->callLoadRequest();
        $this->assertSame('CAT_W', $out['cat']);
        $this->assertSame('', $out['camp']);

        // With mapping
        $_GET['cat'] = 'CAT_Z';
        $out2 = $this->callLoadRequest();
        $this->assertSame('CAT_Z', $out2['cat']);
        $this->assertSame('CAMP_Z', $out2['camp']);
    }
}
}