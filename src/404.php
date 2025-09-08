<?php
http_response_code(404);
//---
include_once __DIR__ . '/include_all.php';
include_once __DIR__ . '/header.php';
//---
function print_h3_title($h3_title)
{
	echo <<<HTML
    <div class="card-header aligncenter" style="font-weight:bold;">
        <h3>$h3_title</h3>
    </div>
    <div class="card-body">
HTML;
}
//---
print_h3_title("404 Error.");

echo <<<HTML
<div class="wrapper">
	<div class="header">
		<p>The page you requested was not found.</p>
	</div>
</div>
HTML;
