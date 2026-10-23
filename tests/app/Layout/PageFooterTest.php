<?php



namespace Tests\App\Layout;

use App\Layout\PageFooter;
use PHPUnit\Framework\TestCase;

use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PageFooter::class)]
class PageFooterTest extends TestCase
{
    public function testRenderWithTimeStart(): void
    {
        $footer = new PageFooter();
        $startTime = microtime(true) - 0.5;

        ob_start();
        $footer->render($startTime);
        $output = ob_get_clean();

        $this->assertStringContainsString('<script>', $output);
        $this->assertStringContainsString('Load Time:', $output);
        $this->assertStringContainsString('card-widget.js', $output);
        $this->assertStringContainsString('</html>', $output);
    }

    public function testRenderWithoutTimeStart(): void
    {
        $footer = new PageFooter();

        ob_start();
        $footer->render(null);
        $output = ob_get_clean();

        $this->assertStringNotContainsString('Load Time:', $output);
        $this->assertStringContainsString('card-widget.js', $output);
        $this->assertStringContainsString('</html>', $output);
    }
}
