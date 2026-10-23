<?php



namespace Tests\App\Layout;

use App\Layout\PageHeader;
use App\User\CurrentUser;
use PHPUnit\Framework\TestCase;

use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PageHeader::class)]
class PageHeaderTest extends TestCase
{
    public function testRenderWithGuestUser(): void
    {
        $currentUser = $this->createMock(CurrentUser::class);
        $currentUser->method('isLoggedIn')->willReturn(false);
        $currentUser->method('isCoordinator')->willReturn(false);
        $currentUser->method('getAlertMessage')->willReturn(null);

        $pageHeader = new PageHeader($currentUser);

        ob_start();
        $pageHeader->render(false);
        $output = ob_get_clean();

        $this->assertStringContainsString('<!DOCTYPE html>', $output);
        $this->assertStringContainsString('Login', $output);
        $this->assertStringContainsString('Tools', $output);
        $this->assertStringContainsString('<main id=\'body\'>', $output);
    }

    public function testRenderWithLoggedInCoordinatorUser(): void
    {
        $currentUser = $this->createMock(CurrentUser::class);
        $currentUser->method('isLoggedIn')->willReturn(true);
        $currentUser->method('getUsername')->willReturn('AdminUser');
        $currentUser->method('isCoordinator')->willReturn(true);
        $currentUser->method('getAlertMessage')->willReturn('Warning alert message');

        $pageHeader = new PageHeader($currentUser);

        ob_start();
        $pageHeader->render(false);
        $output = ob_get_clean();

        $this->assertStringContainsString('AdminUser', $output);
        $this->assertStringContainsString('Coordinator Tools', $output);
        $this->assertStringContainsString('Warning alert message', $output);
    }

    public function testRenderWithHideNavTrue(): void
    {
        $currentUser = $this->createMock(CurrentUser::class);
        $currentUser->method('getAlertMessage')->willReturn(null);

        $pageHeader = new PageHeader($currentUser);

        ob_start();
        $pageHeader->render(true);
        $output = ob_get_clean();

        $this->assertStringContainsString('<!DOCTYPE html>', $output);
        $this->assertStringNotContainsString('mainnav', $output);
        $this->assertStringContainsString('<main id=\'body\'>', $output);
    }
}
