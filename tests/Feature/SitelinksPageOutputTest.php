<?php
declare(strict_types=1);

/**
 * Testing library/framework: PHPUnit.
 * These tests validate the end-to-end HTML emitted by the script using stubbed dependencies.
 */
use PHPUnit\Framework\TestCase;

final class SitelinksPageOutputTest extends TestCase
{
    /**
     * Define namespace-scoped stub functions and inject deterministic dataset.
     */
    private function defineStubs(array $dataset): void
    {
        $GLOBALS['__sitelinks_data'] = $dataset;

        if (!\function_exists('\\Actions\\TestPrint\\test_print')) {
            eval('namespace Actions\\TestPrint { function test_print(string $s): void { $GLOBALS["__test_prints"][] = $s; } }');
        }
        if (!\function_exists('\\Tables\\TablesDir\\open_td_Tables_file')) {
            eval('namespace Tables\\TablesDir { function open_td_Tables_file(string $file) { return $GLOBALS["__sitelinks_data"]; } }');
        }
    }

    private function dataSet(): array
    {
        return [
            'heads' => ['en', 'de', 'commons'], // "commons" should be removed by array_diff
            'qids'  => [
                'Q100' => ['sitelinks' => ['en' => 'Foo_En', 'de' => 'Foo_De'], 'mdtitle' => 'Foo'],
                'Q200' => ['sitelinks' => ['de' => 'Nur_De'],                     'mdtitle' => 'Bar'],
                'Q300' => ['sitelinks' => [],                                      'mdtitle' => 'NoLinks'],
            ],
        ];
    }

    /**
     * Default view (site=all, no items_with_no_links).
     * Expect: heads "en" and "de" shown, anchor text "O" for sitelinks, counts without with_site note.
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_default_all_heads_shows_O_anchors(): void
    {
        $this->defineStubs($this->dataSet());
        $_GET = []; // defaults

        ob_start();
        require __DIR__ . '/../SitelinksTest.php';
        $html = ob_get_clean();

        $this->assertStringContainsString('Heads: 2, Qids: 3', $html);
        $this->assertStringNotContainsString('(with site:', $html);
        $this->assertStringContainsString('<th>en</th>', $html);
        $this->assertStringContainsString('<th>de</th>', $html);

        // "O" anchors for each present sitelink when not filtering by a specific site
        $this->assertMatchesRegularExpression('#https://en\.wikipedia\.org/wiki/Foo_En">O</a>#', $html);
        $this->assertMatchesRegularExpression('#https://de\.wikipedia\.org/wiki/Foo_De">O</a>#', $html);
    }

    /**
     * Filter by specific site (site=en).
     * Expect: only "en" head column, with_site note present with correct counts, anchor text shows title (not "O").
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_filter_by_site_renders_only_that_head_and_counts(): void
    {
        $this->defineStubs($this->dataSet());
        $_GET = ['site' => 'en'];

        ob_start();
        require __DIR__ . '/../SitelinksTest.php';
        $html = ob_get_clean();

        $this->assertStringContainsString('Heads: 2, Qids: 3 (with site: 1, no site link: 2)', $html);
        $this->assertStringContainsString('<th>en</th>', $html);
        $this->assertStringNotContainsString('<th>de</th>', $html);

        // For specific site, the anchor text should be the actual title value
        $this->assertMatchesRegularExpression('#https://en\.wikipedia\.org/wiki/Foo_En">Foo_En</a>#', $html);

        // No other wiki domains should appear
        $this->assertStringNotContainsString('https://de.wikipedia.org/wiki', $html);
    }

    /**
     * Items with no links only.
     * Expect: only QIDs with zero sitelinks, no head columns.
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_items_with_no_links_shows_only_zero_sitelinks(): void
    {
        $this->defineStubs($this->dataSet());
        $_GET = ['items_with_no_links' => '1'];

        ob_start();
        require __DIR__ . '/../SitelinksTest.php';
        $html = ob_get_clean();

        // Only Q300 should be listed
        $this->assertStringContainsString("<a href='https://wikidata.org/wiki/Q300'>Q300</a>", $html);
        $this->assertStringNotContainsString("<a href='https://wikidata.org/wiki/Q100'>Q100</a>", $html);
        $this->assertStringNotContainsString("<a href='https://wikidata.org/wiki/Q200'>Q200</a>", $html);

        // No site columns when filtering for no links
        $this->assertStringNotContainsString('<th>en</th>', $html);
        $this->assertStringNotContainsString('<th>de</th>', $html);
    }

    /**
     * Ensure the site GET parameter is safely escaped into the form value.
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_site_param_is_escaped_in_form_value(): void
    {
        $this->defineStubs($this->dataSet());
        $raw = '\'><script>alert(1)</script>';
        $_GET = ['site' => $raw];

        ob_start();
        require __DIR__ . '/../SitelinksTest.php';
        $html = ob_get_clean();

        $escaped = \htmlspecialchars($raw, ENT_QUOTES, 'UTF-8');
        $this->assertMatchesRegularExpression('/<input[^>]*id="site"[^>]*value="' . \preg_quote($escaped, '/') . '"[^>]*>/', $html);
    }
}