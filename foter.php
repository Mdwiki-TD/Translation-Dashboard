</div>
</main>

<script>
function login(){
	var test = $('#test').val();
	var cat = $('#cat').val();
	var code = $('#code').val();
	var type = $('input[name=type]:checked').val();
	var url = 'login5.php?action=login&code=' + code + '&cat=' + cat + '&type=' + type + '&test=' + test;
	// alert(url);
	window.location.href = url;
}
$(document).ready(function() {
	// console.log('usernamexx');
    to_get();
    $('[data-toggle="tooltip"]').tooltip();
    var table = $('.soro').DataTable({
    // paging: false,
	lengthMenu: [[25, 50, 100, 200], [25, 50, 100, 200]],
    // scrollY: 400,
	// order: [[2	, 'desc']],
	});
});
</script>
<!-- Footer -->
<!-- 
<footer class='app-footer'>
</footer>
 -->
</body>
</html> 
