<?php

declare(strict_types=1);

namespace Tests\App\ApiClients;

use App\ApiClients\WikiApi;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(WikiApi::class)]
class WikiApiTest extends TestCase
{
    public function testMakeViewByNumberNumericAndNonNumeric(): void
    {
        $linkNumeric = WikiApi::makeViewByNumber('Target_Page', 100, 'en', '2023-01-01');
        $this->assertStringContainsString('https://pageviews.wmcloud.org/?', $linkNumeric);
        $this->assertStringContainsString('100', $linkNumeric);
        $this->assertStringContainsString("target='_blank'", $linkNumeric);

        $linkNonNumeric = WikiApi::makeViewByNumber('Target_Page', '?', 'en', null);
        $this->assertStringContainsString('https://wikimedia.org/api/rest_v1/metrics/pageviews/per-article/', $linkNonNumeric);
        $this->assertStringContainsString("name='toget'", $linkNonNumeric);
    }

    public function testGetViewsReturnsZeroForEmptyTarget(): void
    {
        $views = WikiApi::getViews('', 'en', '2023-01-01');
        $this->assertSame(0, $views);
    }

    public function testGetUrlResultCurl(): void
    {
        ob_start();
        $output = WikiApi::getUrlResultCurl('invalid_url_scheme://test');
        ob_end_clean();

        $this->assertSame('', $output);
    }
}
