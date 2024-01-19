</div>
</main>

<script>
	function pupwindow(url) {
		window.open(url, 'popupWindow', 'width=850,height=550,left=50,top=50,scrollbars=yes');
	};
	function pupwindow1(url) {
		window.open(url, 'popupWindow', 'width=500,height=350, left=100, top=100, location=no');
	};

	function login() {
		var cat = $('#cat').val() || '';
		var code = $('#code').val() || '';
		var type = $('input[name=type]:checked').val() || '';
		var test = $('#test').val() || '';
		var url = 'auth.php?a=login';
		if (cat !== '') {
			url += '&cat=' + cat;
		}
		if (code !== '') {
			url += '&code=' + code;
		}
		if (type !== '') {
			url += '&type=' + type;
		}
		if (test !== '') {
			url += '&test=' + test;
		}

		window.location.href = url;
	}

	$('.sortable').DataTable({
		paging: false,
		info: false,
		searching: false
	});
	$('.sortable2').DataTable({
		lengthMenu: [
			[25, 50, 100, 200],
			[25, 50, 100, 200]
		],
	});
	$(document).ready(function() {
		// Call to_get() function
		to_get();

		$('[data-toggle="tooltip"]').tooltip();

		setTimeout(function() {
			$('.soro').DataTable({
				lengthMenu: [
					[25, 50, 100, 200],
					[25, 50, 100, 200]
				],
			});
		}, 3000);
	});
</script>
<!-- Footer -->
<!-- 
<footer class='app-footer'>
</footer>
 -->
</body>

</html>