<?php



namespace Tests\App\User;

use App\User\SessionManager;
use PHPUnit\Framework\TestCase;

use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SessionManager::class)]
class SessionManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        $_SESSION = [];
        parent::tearDown();
    }

    public function testEnsureStarted(): void
    {
        $this->assertSame(PHP_SESSION_NONE, session_status());

        SessionManager::ensureStarted();

        $this->assertSame(PHP_SESSION_ACTIVE, session_status());
    }

    public function testEnsureStartedWhenAlreadyActive(): void
    {
        @session_start();
        $this->assertSame(PHP_SESSION_ACTIVE, session_status());

        $_SESSION['test_key'] = 'test_value';
        SessionManager::ensureStarted();

        $this->assertSame('test_value', $_SESSION['test_key']);
    }

    public function testDestroy(): void
    {
        SessionManager::ensureStarted();
        $_SESSION['user'] = 'john_doe';

        SessionManager::destroy();

        $this->assertEmpty($_SESSION);
        $this->assertSame(PHP_SESSION_NONE, session_status());
    }
}
