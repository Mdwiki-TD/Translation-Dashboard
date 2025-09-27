<?php

if (isset($GLOBALS['time_start'])) {
	$time_start = $GLOBALS['time_start'];
	$time_end = microtime(true);
	$time_diff = $time_end - $time_start;
	$time_diff = round($time_diff, 3);
	//---
	$line = "Load Time: " . $time_diff . " seconds";
	//---
	$script = "$('.tool_title').attr('title', '$line');";
	//---
	// if (isset($_REQUEST['test']) || isset($_COOKIE['test']) || $_SERVER["SERVER_NAME"] == "localhost") {
	// $script .= "\n\t$('#load_time').html('$line');";
	// }
	//---
	echo "\n<script>\n\t $script</script>";
}
?>

</div>
</main>
<script src="/Translation_Dashboard/js/c.js"></script>
<script>
	const cookieAlert = document.getElementById('cookie-alert');
	if (cookieAlert) {
		cookieAlert.addEventListener('close.bs.alert', function() {
			document.cookie = "cookie_alert_dismissed=true; max-age=31536000; path=/; Secure; SameSite=Lax";
		});
	}

	function copy_target_text(id) {
		let textarea = document.getElementById(id);
		textarea.select();
		document.execCommand("copy");
	}
	$(".Dropdown_menu_toggle").on("click", function() {
		$(".div_menu").toggleClass("mactive");
	});

	$('.sortable').DataTable({
		stateSave: true,
		paging: false,
		info: false,
		searching: false
	});
	$('.sortable2').DataTable({
		stateSave: true,
		lengthMenu: [
			[25, 50, 100, 200],
			[25, 50, 100, 200]
		],
	});
	$(document).ready(function() {
		// Call get_views() function
		get_views();

		// $('[data-toggle="tooltip"]').tooltip();
		const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
		const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

		// $('.card').CardWidget('toggle')
		$('.table_responsive').DataTable({
			paging: false,
			info: false,
			searching: false,
			responsive: {
				details: true
				// display: $.fn.dataTable.Responsive.display.modal()
			}
		});

		setTimeout(function() {
			$('.soro').DataTable({
				stateSave: true,
				lengthMenu: [
					[25, 50, 100, 200],
					[25, 50, 100, 200]
				],
			});
		}, 200);
	});
</script>
</body>

</html>
