<?php



namespace Tests\App\User;

use App\Settings;
use App\User\UserCookieService;
use Defuse\Crypto\Key;
use PHPUnit\Framework\TestCase;

use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UserCookieService::class)]
class UserCookieServiceTest extends TestCase
{
    private array $originalCookie;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalCookie = $_COOKIE;
    }

    protected function tearDown(): void
    {
        $_COOKIE = $this->originalCookie;
        parent::tearDown();
    }

    public function testReadEmptyWhenCookieNotSet(): void
    {
        unset($_COOKIE['username']);
        $settings = Settings::getInstance();
        $service = new UserCookieService($settings);

        $this->assertSame('', $service->read());
    }

    public function testWriteAndReadCookie(): void
    {
        $settings = Settings::getInstance();
        $key = Key::createNewRandomKey();

        // Reflection to set cookie key on Settings for testing
        $ref = new \ReflectionClass($settings);
        $prop = $ref->getProperty('cookieKey');
        $prop->setValue($settings, $key);

        $service = new UserCookieService($settings);
        $username = 'TestUser';

        $encrypted = $settings->encodeValue($username, $key);
        $_COOKIE['username'] = $encrypted;

        $this->assertSame('TestUser', $service->read());
    }

    public function testReadReplacesPlusWithSpace(): void
    {
        $settings = Settings::getInstance();
        $key = Key::createNewRandomKey();

        $ref = new \ReflectionClass($settings);
        $prop = $ref->getProperty('cookieKey');
        $prop->setValue($settings, $key);

        $service = new UserCookieService($settings);

        $encrypted = $settings->encodeValue('John+Doe', $key);
        $_COOKIE['username'] = $encrypted;

        $this->assertSame('John Doe', $service->read());
    }
}
