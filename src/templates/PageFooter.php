<?php
// src/templates/PageFooter.php

namespace App\Templates;

use App\User\CurrentUser;

class PageFooter
{
	private currentUser $currentUser;

	public function __construct(CurrentUser $currentUser)
	{
		$this->currentUser = $currentUser;
	}

	/**
	 * Builds the inline <script> that reports page load time via the
	 * tool_title tooltip attribute. Returns '' when no start time is set.
	 *
	 * @param float|null $timeStart Microtime captured at the start of the request
	 *                               (e.g. from PageHeader::getLoadStartTime()).
	 *                               Pass null to skip the load-time script.
	 */
	private function loadTimeScript(?float $timeStart = null): string
	{
		if ($timeStart === null) {
			return '';
		}

		$timeEnd  = microtime(true);
		$timeDiff = round($timeEnd - $timeStart, 3);

		$line = "Load Time: {$timeDiff} seconds";

		// Escape for JavaScript
		$escapedLine = addslashes($line);
		$script = "$('.tool_title').attr('title', '{$escapedLine}');";

		return "\n<script>\n\t{$script}</script>";
	}

	/**
	 * Renders the closing markup: load-time script, closing tags,
	 * shared JS includes, and page-wide JS initialization.
	 */
	public function render(?float $timeStart = null): void
	{
		if ($this->currentUser->isLoggedIn()) {
			if (!isset($_COOKIE['cookie_alert_dismissed1'])) {
				echo $this->cokkieMsg();
			}
		}

		echo $this->loadTimeScript($timeStart);

		echo <<<HTML

        </div>
        </main>

        <script src="/Translation_Dashboard/js/footer.js"></script>

        <!-- Common JavaScript -->
        <script src="/Translation_Dashboard/js/c.js"></script>
        </body>

        </html>
        HTML;
	}
	private function cokkieMsg(): string {
		return <<<HTML
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
