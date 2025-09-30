<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Results\GetCats\CategoryFetcher;

final class CategoryFetcherTest extends TestCase
{
    private string $tmpDir;

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . '/catfetcher_test_' . uniqid();
        mkdir($this->tmpDir . '/cats_cash', 0777, true);

        // Root category contains a subcategory, a normal page and a File: (which should be excluded)
        file_put_contents(
            $this->tmpDir . '/cats_cash/Root.json',
            json_encode(['list' => ['Category:Sub', 'Page A', 'File:IgnoreMe']])
        );

        // Subcategory contains pages, another subcategory and a disambiguation page
        file_put_contents(
            $this->tmpDir . '/cats_cash/Category:Sub.json',
            json_encode(['list' => ['Page B', 'Category:Sub2', 'Page C (disambiguation)']])
        );

        // Sub2 contains a page and a User: (which should be excluded)
        file_put_contents(
            $this->tmpDir . '/cats_cash/Category:Sub2.json',
            json_encode(['list' => ['Page D', 'User:Someone']])
        );
    }

    protected function tearDown(): void
    {
        $this->rrmdir($this->tmpDir);
    }

    private function rrmdir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $this->rrmdir($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($dir);
    }

    public function testGetMdwikiCatMembersDepth1UsesCacheAndFilters(): void
    {
        $options = ['tablesDir' => $this->tmpDir, 'debug' => false];
        $fetcher = new CategoryFetcher($options, 'https://mdwiki.org/w/api.php');

        // depth = 1 should include pages from Root and from Category:Sub
        $result = $fetcher->getMdwikiCatMembers('Root', 1, true);

        sort($result);
        $this->assertSame(['Page A', 'Page B'], $result);
    }

    public function testGetMdwikiCatMembersDepth0DoesNotIncludeSubcatPages(): void
    {
        $options = ['tablesDir' => $this->tmpDir, 'debug' => false];
        $fetcher = new CategoryFetcher($options, 'https://mdwiki.org/w/api.php');

        // depth = 0 should return only direct members of Root (Page A)
        $result = $fetcher->getMdwikiCatMembers('Root', 0, true);

        $this->assertSame(['Page A'], $result);
    }

    public function testGetCatsFromCacheRespectsNoCacheOption(): void
    {
        $options = ['tablesDir' => $this->tmpDir, 'debug' => false, 'nocache' => true];
        $fetcher = new CategoryFetcher($options, 'https://mdwiki.org/w/api.php');

        // nocache => getCatsFromCache must return empty array
        $result = $fetcher->getCatsFromCache('Root');
        $this->assertSame([], $result);
    }

    public function testTitlesFiltersExcludesTemplatesUserAndDisambiguation(): void
    {
        $options = ['tablesDir' => $this->tmpDir, 'debug' => false];
        $fetcher = new CategoryFetcher($options, 'https://mdwiki.org/w/api.php');

        // titlesFilters is private; use Reflection to call it directly to validate filtering logic
        $ref = new ReflectionClass($fetcher);
        $method = $ref->getMethod('titlesFilters');
        $method->setAccessible(true);

        $input = ['Page X', 'File:Foo', 'User:Bar', 'Thing (disambiguation)'];
        $filtered = $method->invokeArgs($fetcher, [$input, false]);

        // Only 'Page X' must remain
        $this->assertSame(['Page X'], array_values($filtered));
    }
}
