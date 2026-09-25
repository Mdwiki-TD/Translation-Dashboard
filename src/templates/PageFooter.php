<?php

use User\CurrentUser;

$currentUser = CurrentUser::getInstance();

if ($currentUser->isLoggedIn()) {
	if (!isset($_COOKIE['cookie_alert_dismissed1'])) {
		echo <<<HTML
			<div id="cookie-alert" class="alert alert-dismissible fade show" role="alert">
				<div class="d-flex align-items-center justify-content-center text-center fixed-bottom">
					<div class="card border-warning m-1">
						<div class="w-100 d-flex justify-content-end">
							<button type="button" class="btn btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						</div>
						<div class="card-body me-5">
							<p class="card-text">This website uses cookies to save your username for a better experience.</p>
						</div>
						<div class="card-footer text-muted">
							<button type="button" class="btn btn-sm btn-success" onclick="acceptCookieAlert()" data-bs-dismiss="alert" aria-label="Close">Accept</button>
							<button type="button" class="btn btn-sm btn-warning" data-bs-dismiss="alert" aria-label="Close">Dismiss</button>
						</div>
					</div>
				</div>
			</div>
		HTML;
	}
}

// Calculate and display page load time
if (isset($GLOBALS['time_start'])) {
	$time_start = (float)$GLOBALS['time_start'];
	$time_end = microtime(true);
	$time_diff = round($time_end - $time_start, 3);

	$line = "Load Time: {$time_diff} seconds";

	// Escape for JavaScript
	$escaped_line = addslashes($line);
	$script = "$('.tool_title').attr('title', '{$escaped_line}');";

	echo "\n<script>\n\t{$script}</script>";
}
?>

</div>
</main>

<!-- Common JavaScript -->
<script src="/Translation_Dashboard/js/c.js"></script>
<script src="/Translation_Dashboard/js/footer.js"></script>
</body>

</html>
