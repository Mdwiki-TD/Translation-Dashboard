</div>
</main>

<script>
	
function login(){
	var cat = $('#cat').val();
	var code = $('#code').val();
	var type = $('input[name=type]:checked').val();

	var url = 'login5.php?action=login&code=' + code + '&cat=' + cat + '&type=' + type;
	alert(url);
	window.location.href = url;
}

$(document).ready(function() {
	// console.log('usernamexx');
    to_get();
});
</script>
<!-- Footer -->
<!-- 
<footer class='app-footer'>
</footer>
 -->
</body>
</html> 