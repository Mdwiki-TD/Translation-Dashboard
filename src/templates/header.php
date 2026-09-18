<!DOCTYPE html>
<?php

// Track page load time for performance monitoring
$time_start = microtime(true);

use OAuth\Settings\Settings;
use function TDC\Head\print_full_head;
use function TDC\Head\write_body;

include_once __DIR__ . '/head.php';

echo print_full_head();

$settings = Settings::getInstance();

[$username, $user_is_coordinator] = load_user($settings);

$coord_tools = "";

// Check if current user is a coordinator
if ($user_is_coordinator === true) {
	$coord_tools = '<a href="/tdc/index.php" class="nav-link py-2 px-0 px-lg-2"><span class="navtitles"></span> <i class="bi bi-tools me-1"></i> Coordinator Tools</a>';
}

// Generate user menu based on authentication state
$li_user = <<<HTML
	<li class="nav-item col-lg-auto col-md-4 col-sm-6 col-6">
		<a href="/auth/login.php" class="nav-link py-2 px-0 px-lg-2">
			<i class="fas fa-sign-in-alt fa-sm fa-fw mr-2"></i> Login
		</a>
	</li>
HTML;

if (!empty($username)) {
	$li_user = <<<HTML
		<li class="nav-item col-lg-auto col-md-4 col-sm-6 col-6">
			<a href="/Translation_Dashboard/leaderboard.php?get=users&user={$username}" class="nav-link py-2 px-0 px-lg-2">
				<i class="fas fa-user fa-sm fa-fw mr-2"></i> <span class="navtitles">{$username}</span>
			</a>
		</li>
		<li class="nav-item col-lg-auto col-md-4 col-sm-6 col-6">
			<a class="nav-link py-2 px-0 px-lg-2" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
				<i class="fas fa-sign-out-alt fa-sm fa-fw mr-2"></i> <span class="d-lg-none navtitles">Logout</span>
			</a>
		</li>
	HTML;
}

echo "<body>";

// Output HTML header and navigation
echo write_body($coord_tools, $li_user);

echo "<main id='body'><div id='maindiv' class='container-fluid'>";

?>
