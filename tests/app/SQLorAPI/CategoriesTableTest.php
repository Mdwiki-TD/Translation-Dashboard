<?php

declare(strict_types=1);

namespace Tests\App\SQLorAPI;

use App\SQLorAPI\ApiOrSqlService;
use App\SQLorAPI\CategoriesTable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CategoriesTable::class)]
class CategoriesTableTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        CategoriesTable::resetCache();
    }

    protected function tearDown(): void
    {
        CategoriesTable::resetCache();
        parent::tearDown();
    }

    public function testGetCategoriesAndCaching(): void
    {
        $service = $this->createMock(ApiOrSqlService::class);
        $service->expects($this->once())
            ->method('superFunction')
            ->willReturn([
                ['id' => 1, 'category' => 'Cat1', 'campaign' => 'Camp1']
            ]);

        $table = new CategoriesTable($service);

        $res1 = $table->getCategories();
        $res2 = $table->getCategories();

        $this->assertSame($res1, $res2);
        $this->assertCount(1, $res1);
    }

    public function testGetCategoriesMembers(): void
    {
        $service = $this->createMock(ApiOrSqlService::class);
        $service->expects($this->once())
            ->method('superFunction')
            ->with(['get' => 'category_members', 'cat' => 'RTT'], ['RTT'], $this->anything())
            ->willReturn([
                ['article_id' => 'Article1'],
                ['article_id' => 'Article2']
            ]);

        $table = new CategoriesTable($service);
        $members = $table->getCategoriesMembers('RTT');

        $this->assertSame(['Article1', 'Article2'], $members);
    }

    public function testGetCampsToCat(): void
    {
        $service = $this->createMock(ApiOrSqlService::class);
        $service->expects($this->once())
            ->method('superFunction')
            ->willReturn([
                ['id' => 1, 'category' => 'Cat1', 'campaign' => 'Camp1'],
                ['id' => 2, 'category' => 'Cat2', 'campaign' => 'Camp2']
            ]);

        $table = new CategoriesTable($service);
        $camps = $table->getCampsToCat();

        $this->assertSame(['Camp1' => 'Cat1', 'Camp2' => 'Cat2'], $camps);
    }

    public function testCountCategoryMembers(): void
    {
        $service = $this->createMock(ApiOrSqlService::class);
        $service->expects($this->once())
            ->method('superFunction')
            ->with([], ['RTT'], $this->anything())
            ->willReturn([['members' => 42]]);

        $table = new CategoriesTable($service);
        $count = $table->countCategoryMembers(null);

        $this->assertSame([['members' => 42]], $count);
    }

    public function testMissingAndExistsByLangAndCategory(): void
    {
        $service = $this->createMock(ApiOrSqlService::class);
        $service->expects($this->exactly(2))
            ->method('superFunction')
            ->willReturn([['title' => 'SampleTitle']]);

        $table = new CategoriesTable($service);

        $missing = $table->missingByLangAndCategory('ar', 'RTT');
        $this->assertSame([['title' => 'SampleTitle']], $missing);

        $exists = $table->existsByLangAndCategory('ar', 'RTT');
        $this->assertSame([['title' => 'SampleTitle']], $exists);
    }

    public function testStaticsByCategory(): void
    {
        $service = $this->createMock(ApiOrSqlService::class);
        $service->expects($this->once())
            ->method('superFunction')
            ->with(['get' => 'statics_by_category', 'category' => 'RTT'], ['RTT'], $this->anything())
            ->willReturn([['language_code' => 'en', 'available_title_count' => 10]]);

        $table = new CategoriesTable($service);
        $stats = $table->staticsByCategory(null);

        $this->assertSame([['language_code' => 'en', 'available_title_count' => 10]], $stats);
    }
}
