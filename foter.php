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
    $('.soro').DataTable({
    paging: false,
    scrollY: 400
	});
    $('.soro2').DataTable({});
});
</script>
<!-- Footer -->
<!-- 
<footer class='app-footer'>
</footer>
 -->
</body>
</html> 
