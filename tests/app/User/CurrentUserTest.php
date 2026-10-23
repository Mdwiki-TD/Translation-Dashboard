<?php

declare(strict_types=1);

namespace Tests\App\User;

use App\Settings;
use App\User\AccessKeyRepository;
use App\User\CoordinatorRepository;
use App\User\CurrentUser;
use App\User\UserCookieService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CurrentUser::class)]
class CurrentUserTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
    }

    public function testCurrentUserWithMockedDependencies(): void
    {
        $settings = Settings::getInstance();

        $cookies = $this->createMock(UserCookieService::class);
        $cookies->method('read')->willReturn('test_user');

        $accessKeys = $this->createMock(AccessKeyRepository::class);

        $coordinators = $this->createMock(CoordinatorRepository::class);
        $coordinators->method('isCoordinator')->with('test_user')->willReturn(true);
        $coordinators->method('isAdmin')->with('test_user')->willReturn(false);

        $user = new CurrentUser($settings, $cookies, $accessKeys, $coordinators);

        $this->assertSame('test_user', $user->getUsername());
        $this->assertTrue($user->isLoggedIn());
        $this->assertTrue($user->isCoordinator());
        $this->assertFalse($user->isAdmin());
        $this->assertNull($user->getAlertMessage());
    }

    public function testDestroyClearsState(): void
    {
        $settings = Settings::getInstance();

        $cookies = $this->createMock(UserCookieService::class);
        $cookies->method('read')->willReturn('test_user');
        $cookies->expects($this->once())->method('clear');

        $accessKeys = $this->createMock(AccessKeyRepository::class);
        $coordinators = $this->createMock(CoordinatorRepository::class);

        $user = new CurrentUser($settings, $cookies, $accessKeys, $coordinators);
        $user->destroy();

        $this->assertSame('', $user->getUsername());
        $this->assertFalse($user->isLoggedIn());
        $this->assertFalse($user->isCoordinator());
        $this->assertFalse($user->isAdmin());
    }
}
