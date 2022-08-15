</div>
</main>

<script>
	
function login(){
    var cat = $('#cat').val();
    var depth = $('#depth').val();
    var code = $('#code').val();
    var type = $('input[name=type]:checked').val();

    var url = 'login5.php?action=login&code=' + code + '&cat=' + cat + '&depth=' + depth + '&type=' + type;
    window.location.href = url;
}

// $(document).ready(function() {
var lo = $('#myusername').text();
if ( lo != '' ) {
	$('#login_btn').hide();
	$("#doit_btn").show();

	$('#myboard').show();
	$('#loginli').hide();

	$('#logoutli').show();
	$('#user_name').text(lo);

} else {
	$('#login_btn').show();
	$("#doit_btn").hide();

	$('#loginli').show();

	$('#logoutli').hide();
};
// });
</script>
<!-- Footer -->
<!-- 
<footer class='app-footer'>
</footer>
 -->
</body>
</html> 