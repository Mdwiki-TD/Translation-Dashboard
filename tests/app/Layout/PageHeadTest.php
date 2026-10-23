<?php



namespace Tests\App\Layout;

use App\Layout\PageHead;
use PHPUnit\Framework\TestCase;

use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PageHead::class)]
class PageHeadTest extends TestCase
{
    private array $originalServer;
    private array $originalGet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalServer = $_SERVER;
        $this->originalGet = $_GET;
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->originalServer;
        $_GET = $this->originalGet;
        parent::tearDown();
    }

    public function testPrintFullHead(): void
    {
        $pageHead = new PageHead();
        $head = $pageHead->print_full_head();

        $this->assertStringContainsString('<title>Wiki Project Med Translation Dashboard</title>', $head);
        $this->assertStringContainsString('bootstrap.min.css', $head);
        $this->assertStringContainsString('jquery.min.js', $head);
    }

    public function testPrintFullHeadWithNobootGetParam(): void
    {
        $_GET['noboot'] = '1';
        $pageHead = new PageHead();
        $head = $pageHead->print_full_head();

        $this->assertStringContainsString('<title>Wiki Project Med Translation Dashboard</title>', $head);
        $this->assertStringNotContainsString('bootstrap.min.css', $head);
    }

    public function testIsActive(): void
    {
        $_SERVER['PHP_SELF'] = '/path/to/index.php';
        $pageHead = new PageHead();

        $this->assertSame('active', $pageHead->is_active('index.php'));
        $this->assertSame('', $pageHead->is_active('other.php'));
    }

    public function testWriteBody(): void
    {
        $pageHead = new PageHead();
        $body = $pageHead->write_body('<a>Tools</a>', '<li>User</li>');

        $this->assertStringContainsString('<a>Tools</a>', $body);
        $this->assertStringContainsString('<li>User</li>', $body);
        $this->assertStringContainsString('id="logoutModal"', $body);
    }
}
