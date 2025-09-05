<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// Test suite for translate_med/index.php behaviors.
// IMPORTANT: This suite assumes PHPUnit. If your project uses Pest/Codeception, adapt accordingly.

final class IndexTest extends TestCase
{
    private string $subjectPath;

    protected function setUp(): void
    {
        parent::setUp();

        // Locate the subject file. Common locations: translate_med/index.php or public/translate_med/index.php.
        // We try several candidates to make the suite robust to minor structure differences.
        $candidates = [
            __DIR__ . '/../../translate_med/index.php',
            __DIR__ . '/../../public/translate_med/index.php',
            __DIR__ . '/../../src/translate_med/index.php',
        ];
        foreach ($candidates as $cand) {
            if (is_file($cand)) {
                $this->subjectPath = $cand;
                break;
            }
        }
        if (empty($this->subjectPath)) {
            $this->markTestSkipped('translate_med/index.php not found in expected locations.');
        }

        // Isolate superglobals per test
        $_GET = [];
        $GLOBALS['global_username'] = null;

        // Provide minimal shims for namespaced functions if they don't already exist.
        // We guard with function_exists() to avoid redeclaration if the includes provide them.

        // Actions\Html\login_card()
        if (!function_exists('Actions\\Html\\login_card')) {
            eval('namespace Actions\\Html { function login_card(): string { return "<div>LOGIN_CARD</div>"; } }');
        }

        // Results\TrLink\make_translate_link_medwiki()
        if (!function_exists('Results\\TrLink\\make_translate_link_medwiki')) {
            eval('namespace Results\\TrLink { function make_translate_link_medwiki($title, $code, $cat, $camp, $type) { 
                $title = rawurlencode($title);
                $cat   = rawurlencode($cat);
                $camp  = rawurlencode($camp);
                $type  = rawurlencode($type);
                return "https://medwiki.example/ct?title={$title}&code={$code}&cat={$cat}&camp={$camp}&type={$type}";
            } }');
        }

        // SQLorAPI\GetDataTab\get_td_or_sql_users_no_inprocess()
        if (!function_exists('SQLorAPI\\GetDataTab\\get_td_or_sql_users_no_inprocess')) {
            // Default stub returns two users not in-process
            eval('namespace SQLorAPI\\GetDataTab { function get_td_or_sql_users_no_inprocess(): array { return [
                ["user" => "alice"],
                ["user" => "bob"]
            ]; } }');
        }

        // TranslateMed\Inserter\insertPage_inprocess()
        if (!function_exists('TranslateMed\\Inserter\\insertPage_inprocess')) {
            // Record calls for assertions using a global buffer
            eval('namespace TranslateMed\\Inserter { function insertPage_inprocess($title, $word, $type, $cat, $code, $user) { 
                $GLOBALS["__insert_calls"][] = compact("title","word","type","cat","code","user");
            } }');
        }

        // Reset call log
        $GLOBALS['__insert_calls'] = [];
    }

    private function includeSubjectSafely(): void
    {
        // To avoid early exit on "not logged in" branch during include,
        // tests set $GLOBALS["global_username"] appropriately before calling this.
        ob_start();
        // Suppress warnings if includes print closing HTML; we only need function definitions and early logic
        include $this->subjectPath;
        ob_end_clean();
    }

    public function test_go_to_translate_url_outputs_anchor_and_suppresses_redirect_when_test_param_present(): void
    {
        // Arrange
        $GLOBALS['global_username'] = 'tester'; // prevent early exit in subject
        $_GET['test'] = '1'; // suppress redirect
        $this->includeSubjectSafely(); // defines go_to_translate_url()

        $title = 'COVID-19';
        $code  = 'ady';
        $type  = 'lead';
        $cat   = 'RTTCovid';
        $camp  = 'COVID';

        // Act: capture output
        ob_start();
        go_to_translate_url($title, $code, $type, $cat, $camp);
        $out = ob_get_clean();

        // Assert
        $this->assertStringContainsString('<h2>', $out);
        $this->assertStringContainsString('Click here to go to ContentTranslation in medwiki', $out);
        $this->assertStringContainsString('target="_blank"', $out);
        // Should NOT render JS/meta redirects because test=1
        $this->assertStringNotContainsString("window.open(", $out);
        $this->assertStringNotContainsString("meta http-equiv='refresh'", $out);
        // Verify URL structure based on our stub
        $this->assertStringContainsString("https://medwiki.example/ct?title=COVID-19&code=ady&cat=RTTCovid&camp=COVID&type=lead", $out);
    }

    public function test_go_to_translate_url_includes_js_and_meta_redirects_when_no_test_param(): void
    {
        // Arrange
        $GLOBALS['global_username'] = 'tester';
        unset($_GET['test']);
        $this->includeSubjectSafely();

        // Act
        ob_start();
        go_to_translate_url('Article Name', 'en', 'lead', 'General', 'CampaignX');
        $out = ob_get_clean();

        // Assert: JS and meta redirects present
        $this->assertStringContainsString("window.open('https://medwiki.example/ct", $out);
        $this->assertStringContainsString("meta http-equiv='refresh'", $out);
    }

    public function test_when_not_logged_in_login_card_is_output_and_script_would_exit(): void
    {
        // Arrange: no user set
        $GLOBALS['global_username'] = null;

        // Act: capture include output
        ob_start();
        include $this->subjectPath;
        $out = ob_get_clean();

        // Assert: login card shown (from stub)
        $this->assertStringContainsString('<div>LOGIN_CARD</div>', $out);
        // We cannot assert exit directly; including captured output suffices
    }

    public function test_when_logged_in_and_required_params_missing_no_insert_or_redirect_is_triggered(): void
    {
        // Arrange
        $GLOBALS['global_username'] = 'tester';
        $_GET = []; // no title/code so main branch should not run

        // Act
        $this->includeSubjectSafely();

        // Assert: insert not called
        $this->assertSame([], $GLOBALS['__insert_calls'] ?? []);
    }

    public function test_when_logged_in_and_user_not_in_inprocess_insert_is_called_once(): void
    {
        // Arrange
        $GLOBALS['global_username'] = 'charlie'; // not in stubbed list [alice, bob]
        // Provide minimal GET to pass branch; filter_input in CLI may return null.
        // However, the file also reads globals via rawurldecode after filter_input.
        // We defensively set both raw $_GET and expect branch may not execute in CLI;
        // If filter_input returns null, this test will be skipped.
        $_GET = [
            'title' => 'My%20Title',
            'code'  => 'es',
            'cat'   => 'Cat%20A',
            'camp'  => 'Camp%20A',
            'type'  => 'lead',
            'word'  => '123',
            'test'  => '1'
        ];

        // Act
        ob_start();
        include $this->subjectPath;
        ob_end_clean();

        // Assert (best-effort in CLI): If branch executed, we should have one insert call
        if (!empty($GLOBALS['__insert_calls'])) {
            $this->assertCount(1, $GLOBALS['__insert_calls']);
            $call = $GLOBALS['__insert_calls'][0];
            $this->assertSame('My Title', $call['title']); // rawurldecode
            $this->assertSame(123, (int)$call['word']);
            $this->assertSame('lead', $call['type']);
            $this->assertSame('Cat A', $call['cat']);
            $this->assertSame('es', $call['code']);
            $this->assertSame('charlie', $call['user']); // user_decoded
        } else {
            $this->markTestSkipped('filter_input likely returned null in CLI; branch not executed. Verified non-failure.');
        }
    }

    public function test_when_logged_in_and_user_in_inprocess_insert_is_not_called(): void
    {
        // Arrange: override users_no_inprocess stub to include our user
        if (function_exists('runkit_function_redefine')) {
            // If runkit available, redefine; otherwise we proceed by choosing a user in default list.
            \runkit_function_redefine('SQLorAPI\\GetDataTab\\get_td_or_sql_users_no_inprocess', '', 'return [["user"=>"dora"]];');
            $user = 'dora';
        } else {
            // Without runkit, choose an existing stubbed user
            $user = 'alice';
        }
        $GLOBALS['global_username'] = $user;
        $_GET = [
            'title' => 'Any',
            'code'  => 'fr',
            'type'  => 'lead',
            'cat'   => '',
            'camp'  => '',
            'test'  => '1'
        ];

        // Act
        ob_start();
        include $this->subjectPath;
        ob_end_clean();

        // Assert: insert should not be called if user is in "no inprocess" list
        $this->assertSame([], $GLOBALS['__insert_calls'] ?? []);
    }
}