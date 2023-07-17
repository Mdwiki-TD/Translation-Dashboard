</div>
</main>

<script>
	function login() {
		var cat = $('#cat').val() || '';
		var code = $('#code').val() || '';
		var type = $('input[name=type]:checked').val() || '';

		var url = 'login5.php?action=login';
		if (cat !== '') {
			url += '&cat=' + cat;
		}
		if (code !== '') {
			url += '&code=' + code;
		}
		if (type !== '') {
			url += '&type=' + type;
		}

		window.location.href = url;
	}

	$(document).ready(function() {
		// Call to_get() function
		to_get();

		$('[data-toggle="tooltip"]').tooltip();

		setTimeout(function() {
			$('.soro2').DataTable({
				paging: false,
				info: false,
				searching: false
			});

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