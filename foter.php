</div>
</main>

<script>
function login(){
	// var test = $('#test').val();
	var cat = $('#cat').val() || '';
	var code = $('#code').val() || '';
	var type = $('input[name=type]:checked').val() || '';

	// var url = 'login5.php?action=login&code=' + code + '&cat=' + cat + '&type=' + type;
	var url = 'login5.php?action=login';
	if (cat != '') url += '&cat=' + cat;
	if (code != '') url += '&code=' + code;
	if (type != '') url += '&type=' + type;
	// alert(url);
	window.location.href = url;
}

$(document).ready(function() {
	//---
	// var da = 
	to_get();
	// console.log('usernamexx');
	//---
    $('[data-toggle="tooltip"]').tooltip();
	//---

	// wait 5 secound
	setTimeout(function() {
		//---
		var table = $('.soro2').DataTable({
			paging: false,
			info:     false,
			searching: false
		});
		//---
		var table = $('.soro').DataTable({
		// paging: false,
		lengthMenu: [[25, 50, 100, 200], [25, 50, 100, 200]],
		// scrollY: 400,
		// order: [[2	, 'desc']],
		});	
	}, 3000);
	//---
});
</script>
<!-- Footer -->
<!-- 
<footer class='app-footer'>
</footer>
 -->
</body>
</html> 
