<?php



namespace Tests\App;

use App\Settings;
use Defuse\Crypto\Key;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Settings::class)]
class SettingsTest extends TestCase
{
    private array $originalServer;
    private array $originalEnv;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalServer = $_SERVER;
        $this->originalEnv = $_ENV;
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->originalServer;
        $_ENV = $this->originalEnv;
        parent::tearDown();
    }

    public function testGetInstance(): void
    {
        $instance1 = Settings::getInstance();
        $instance2 = Settings::getInstance();

        $this->assertInstanceOf(Settings::class, $instance1);
        $this->assertSame($instance1, $instance2);
    }

    public function testEnvironmentHelpers(): void
    {
        $settings = Settings::getInstance();

        // Testing based on bootstrap putenv('APP_ENV=testing')
        $this->assertTrue($settings->isTesting());
        $this->assertFalse($settings->isDevelopment());
        $this->assertFalse($settings->isProduction());
    }

    public function testGenerateCallbackUrl(): void
    {
        $settings = Settings::getInstance();
        $url = $settings->generateCallbackUrl('auth/callback.php');

        $this->assertStringStartsWith('http://', $url);
        $this->assertStringEndsWith('/auth/callback.php', $url);
    }

    public function testPropertyAccess(): void
    {
        $settings = Settings::getInstance();

        $this->assertNotEmpty($settings->domain);
        $this->assertNotEmpty($settings->userAgent);
    }

    public function testMagicSetterRestriction(): void
    {
        $settings = Settings::getInstance();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Settings are read-only. Cannot set: nonExistentPublicProp');
        /** @phpstan-ignore-line */
        $settings->nonExistentPublicProp = 'example.com';
    }

    public function testUndefinedPropertyAccessThrowsException(): void
    {
        $settings = Settings::getInstance();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Undefined setting: nonExistentProperty');
        /** @phpstan-ignore-line */
        $foo = $settings->nonExistentProperty;
    }

    public function testEncodeAndDecodeValue(): void
    {
        $settings = Settings::getInstance();
        $key = Key::createNewRandomKey();

        $plainText = 'secret_data_123';
        $encoded = $settings->encodeValue($plainText, $key);

        $this->assertNotEmpty($encoded);
        $this->assertNotEquals($plainText, $encoded);

        $decoded = $settings->decodeValue($encoded, $key);
        $this->assertSame($plainText, $decoded);
    }

    public function testEncodeAndDecodeWithNullKeyReturnsEmpty(): void
    {
        $settings = Settings::getInstance();

        $this->assertSame('', $settings->encodeValue('test', null));
        $this->assertSame('', $settings->decodeValue('test', null));
    }
}
