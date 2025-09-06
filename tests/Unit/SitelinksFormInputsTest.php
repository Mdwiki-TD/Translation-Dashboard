<?php
declare(strict_types=1);

/**
 * Testing library/framework: PHPUnit.
 * Note: If the repository uses Pest, these PHPUnit tests will still run under Pest.
 */
use PHPUnit\Framework\TestCase;

final class SitelinksFormInputsTest extends TestCase
{
    protected function setUp(): void
    {
        $this->loadGenerateFormInputs();
    }

    private function loadGenerateFormInputs(): void
    {
        if (\function_exists('generateFormInputs')) {
            return;
        }
        $path = __DIR__ . '/../SitelinksTest.php'; // Source file provided in PR context
        $src  = @file_get_contents($path);
        $this->assertNotFalse($src, "Could not read source file at: {$path}");

        if (!\preg_match('/function\s+generateFormInputs\s*\([^)]*\)\s*\{.*?\}\s*/s', $src, $m)) {
            $this->fail('generateFormInputs() not found in source.');
        }

        // Define the function in the current runtime without executing the rest of the script.
        // Safe because we eval only the matched function block.
        eval($m[0]);
        $this->assertTrue(\function_exists('generateFormInputs'), 'generateFormInputs should be defined after eval.');
    }

    public function test_renders_expected_inputs_and_checked_flag(): void
    {
        $params = [
            'site'        => ['type' => 'text',   'value' => 'en'],
            'heads_limit' => ['type' => 'number', 'value' => 50],
            'title_limit' => ['type' => 'number', 'value' => 150],
        ];

        $html = \generateFormInputs($params, 'checked');

        $this->assertStringContainsString("action='sitelinks.php'", $html);
        $this->assertMatchesRegularExpression('/<input[^>]*id="site"[^>]*type="text"[^>]*value="en"[^>]*>/i', $html);
        $this->assertMatchesRegularExpression('/<input[^>]*id="heads_limit"[^>]*type="number"[^>]*value="50"[^>]*>/i', $html);
        $this->assertMatchesRegularExpression('/<input[^>]*id="title_limit"[^>]*type="number"[^>]*value="150"[^>]*>/i', $html);
        $this->assertMatchesRegularExpression('/<input[^>]*id="switch2"[^>]*name="items_with_no_links"[^>]*checked[^>]*>/i', $html);
        $this->assertStringContainsString('Items with no links', $html);
    }

    public function test_unchecked_when_flag_empty(): void
    {
        $params = [
            'site'        => ['type' => 'text',   'value' => 'all'],
            'heads_limit' => ['type' => 'number', 'value' => 50],
            'title_limit' => ['type' => 'number', 'value' => 150],
        ];

        $html = \generateFormInputs($params, '');

        $this->assertMatchesRegularExpression('/<input[^>]*id="switch2"[^>]*name="items_with_no_links"[^>]*>/i', $html);
        // Extract only the switch input tag and ensure it has no "checked"
        $this->assertTrue(\preg_match('/(<input[^>]*id="switch2"[^>]*>)/i', $html, $m) === 1, 'Switch input not found.');
        $this->assertStringNotContainsString('checked', $m[1], 'Switch should not be checked when flag is empty.');
    }

    public function test_dynamic_param_is_rendered(): void
    {
        $params = [
            'site' => ['type' => 'text', 'value' => 'en'],
            'foo'  => ['type' => 'text', 'value' => 'bar'],
        ];

        $html = \generateFormInputs($params, '');

        $this->assertMatchesRegularExpression('/<input[^>]*id="foo"[^>]*name="foo"[^>]*type="text"[^>]*value="bar"[^>]*>/i', $html);
    }

    public function test_sanitized_values_are_preserved_in_attributes(): void
    {
        $raw  = 'en & "quotes" <tag>';
        $safe = \htmlspecialchars($raw, ENT_QUOTES, 'UTF-8');

        $params = [
            'site' => ['type' => 'text', 'value' => $safe],
        ];

        $html = \generateFormInputs($params, '');
        $this->assertStringContainsString('id="site"', $html);
        $this->assertStringContainsString('value="' . $safe . '"', $html);
    }
}