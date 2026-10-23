<?php



namespace Tests\App\Layout;

use App\Layout\PageRunner;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DummyController
{
    public function __construct(private $currentUser) {}
    public function handleRequest(): void
    {
        echo "<div>Dummy Controller Output</div>";
    }
}

use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PageRunner::class)]
class PageRunnerTest extends TestCase
{
    private array $originalGet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalGet = $_GET;
    }

    protected function tearDown(): void
    {
        $_GET = $this->originalGet;
        parent::tearDown();
    }

    public function testRunExecutesControllerHeaderAndFooter(): void
    {
        ob_start();
        PageRunner::run(DummyController::class);
        $output = ob_get_clean();

        $this->assertStringContainsString('<!DOCTYPE html>', $output);
        $this->assertStringContainsString('<div>Dummy Controller Output</div>', $output);
        $this->assertStringContainsString('</html>', $output);
    }

    public function testRunThrowsExceptionForNonExistentController(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Controller not found: NonExistentControllerClass');

        /** @phpstan-ignore-line */
        PageRunner::run('NonExistentControllerClass');
    }
}
