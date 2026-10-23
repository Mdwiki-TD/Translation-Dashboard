<?php
namespace Tests\App\User;

use App\MdwikiSql\Database;
use App\User\CoordinatorRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CoordinatorRepository::class)]
class CoordinatorRepositoryTest extends TestCase
{
    private Database $dbMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dbMock = $this->createMock(Database::class);
    }

    public function testIsCoordinatorReturnsFalseWhenUsernameIsEmpty(): void
    {
        $repo = new CoordinatorRepository($this->dbMock);
        $this->assertFalse($repo->isCoordinator(''));
    }

    public function testIsCoordinatorReturnsTrueWhenActive(): void
    {
        $this->dbMock->method('fetchQuery')->willReturn([
            ['id' => 1, 'username' => 'Coord1', 'is_active' => 1],
            ['id' => 2, 'username' => 'Coord2', 'is_active' => 0],
        ]);

        $repo = new CoordinatorRepository($this->dbMock);
        $this->assertTrue($repo->isCoordinator('Coord1'));
    }

    public function testIsCoordinatorReturnsFalseWhenInactive(): void
    {
        $this->dbMock->method('fetchQuery')->willReturn([
            ['id' => 1, 'username' => 'Coord1', 'is_active' => 1],
            ['id' => 2, 'username' => 'Coord2', 'is_active' => 0],
        ]);

        $repo = new CoordinatorRepository($this->dbMock);
        $this->assertFalse($repo->isCoordinator('Coord2'));
    }

    public function testIsCoordinatorReturnsFalseWhenNotFound(): void
    {
        $this->dbMock->method('fetchQuery')->willReturn([
            ['id' => 1, 'username' => 'Coord1', 'is_active' => 1],
        ]);

        $repo = new CoordinatorRepository($this->dbMock);
        $this->assertFalse($repo->isCoordinator('NonExistentUser'));
    }
}
