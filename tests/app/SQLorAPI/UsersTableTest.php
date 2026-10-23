<?php

declare(strict_types=1);

namespace Tests\App\SQLorAPI;

use App\MdwikiSql\Database;
use App\SQLorAPI\ApiOrSqlService;
use App\SQLorAPI\UsersTable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(UsersTable::class)]
class UsersTableTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        UsersTable::resetCache();
    }

    protected function tearDown(): void
    {
        UsersTable::resetCache();
        parent::tearDown();
    }

    public function testGetCoordinatorsAndUsersNoInprocessAndFullTranslators(): void
    {
        $service = $this->createMock(ApiOrSqlService::class);
        $service->expects($this->exactly(1))
            ->method('getSql')
            ->willReturnOnConsecutiveCalls(
                [['id' => 1, 'username' => 'coord1', 'is_active' => 1]],
            );

        $service->expects($this->exactly(2))
            ->method('superFunction')
            ->willReturnOnConsecutiveCalls(
                [['id' => 1, 'user' => 'no_proc1', 'is_active' => 1]],
                [['id' => 1, 'user' => 'full1', 'is_active' => 1]]
            );

        $table = new UsersTable($service);

        $this->assertSame([['id' => 1, 'username' => 'coord1', 'is_active' => 1]], $table->getCoordinators());
        $this->assertSame([['id' => 1, 'user' => 'no_proc1', 'is_active' => 1]], $table->getUsersNoInprocess());
        $this->assertSame(['full1'], $table->getFullTranslators('user'));
    }

    public function testGetUserByIdAndUsername(): void
    {
        $service = $this->createMock(ApiOrSqlService::class);
        $table = new UsersTable($service);

        $db = $this->createMock(Database::class);
        $db->expects($this->exactly(2))
            ->method('fetchQuery')
            ->willReturnOnConsecutiveCalls(
                [['user_id' => '101', 'username' => 'john']],
                [['user_id' => '101', 'username' => 'john']]
            );

        $table->db = $db;

        $user1 = $table->get_user_by_id('101');
        $this->assertSame('john', $user1['username']);

        $user2 = $table->get_user_by_username('john');
        $this->assertSame('101', $user2['user_id']);

        $this->assertNull($table->get_user_by_id(''));
        $this->assertNull($table->get_user_by_username(''));
    }
}
