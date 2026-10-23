<?php



namespace Tests\App\User;

use App\MdwikiSql\Database;
use App\Settings;
use App\User\AccessKeyRepository;
use Defuse\Crypto\Key;
use PHPUnit\Framework\TestCase;

use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AccessKeyRepository::class)]
class AccessKeyRepositoryTest extends TestCase
{
    private Database $dbMock;
    private Settings $settings;
    private Key $cryptKey;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dbMock = $this->createMock(Database::class);
        $this->settings = Settings::getInstance();
        $this->cryptKey = Key::createNewRandomKey();

        $ref = new \ReflectionClass($this->settings);
        $prop = $ref->getProperty('cryptKey');
        $prop->setValue($this->settings, $this->cryptKey);
    }

    public function testFindByUserReturnsEmptyArrayWhenNotFound(): void
    {
        $this->dbMock->method('fetchQuery')->willReturn([]);

        $repo = new AccessKeyRepository($this->dbMock, $this->settings);
        $result = $repo->findByUser('non_existent_user');

        $this->assertSame([], $result);
    }

    public function testFindByUserReturnsDecodedKeys(): void
    {
        $encKey = $this->settings->encodeValue('key123', $this->cryptKey);
        $encSecret = $this->settings->encodeValue('sec123', $this->cryptKey);

        $this->dbMock->method('fetchQuery')->willReturn([
            [
                'access_key' => $encKey,
                'access_secret' => $encSecret
            ]
        ]);

        $repo = new AccessKeyRepository($this->dbMock, $this->settings);
        $result = $repo->findByUser('john_doe');

        $this->assertSame([
            'access_key' => 'key123',
            'access_secret' => 'sec123'
        ], $result);
    }

    public function testEnsureUserExists(): void
    {
        $this->dbMock->expects($this->once())
            ->method('executeQuery')
            ->willReturn(true);

        $repo = new AccessKeyRepository($this->dbMock, $this->settings);
        $this->assertTrue($repo->ensureUserExists('john_doe'));
    }

    public function testSaveUserDataSuccess(): void
    {
        $this->dbMock->expects($this->once())->method('beginTransaction');
        $this->dbMock->expects($this->exactly(2))->method('executeQuery')->willReturn(true);
        $this->dbMock->expects($this->once())->method('commit');

        $repo = new AccessKeyRepository($this->dbMock, $this->settings);
        $repo->saveUserData('john_doe', 'key123', 'sec123');
    }

    public function testSaveUserDataRollbackOnFailure(): void
    {
        $this->dbMock->expects($this->once())->method('beginTransaction');
        $this->dbMock->expects($this->atLeastOnce())->method('executeQuery')->willReturn(false);
        $this->dbMock->expects($this->once())->method('rollback');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to write user data or access keys to database.');

        $repo = new AccessKeyRepository($this->dbMock, $this->settings);
        $repo->saveUserData('john_doe', 'key123', 'sec123');
    }
}
