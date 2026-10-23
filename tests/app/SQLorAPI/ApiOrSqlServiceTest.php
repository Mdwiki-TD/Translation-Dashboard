<?php

declare(strict_types=1);

namespace Tests\App\SQLorAPI;

use App\MdwikiSql\Database;
use App\SQLorAPI\ApiOrSqlService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ApiOrSqlService::class)]
class ApiOrSqlServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        ApiOrSqlService::$useTdApi = null;
        parent::tearDown();
    }

    public function testIsValid(): void
    {
        $this->assertTrue(ApiOrSqlService::isValid('some_value'));
        $this->assertFalse(ApiOrSqlService::isValid('all'));
        $this->assertFalse(ApiOrSqlService::isValid('ALL'));
        $this->assertFalse(ApiOrSqlService::isValid(''));
        $this->assertFalse(ApiOrSqlService::isValid(null));
    }

    public function testUseTdApiOrSqlExplicitFlag(): void
    {
        $db = $this->createMock(Database::class);
        $service = new ApiOrSqlService($db, false);

        $this->assertFalse($service->useTdApiOrSql());
    }

    public function testSuperFunctionReturnsEmptyInTestingEnvWhenNoApiData(): void
    {
        $db = $this->createMock(Database::class);
        $service = new ApiOrSqlService($db, false);

        $result = $service->superFunction(
            ['get' => 'pages'],
            ['user1'],
            'SELECT * FROM pages WHERE user = ?'
        );

        $this->assertSame([], $result);
    }
}
