    </div>
</main>

<script>
    // $(document).ready(function() {
        var lo = $('#myusername').text();
        if ( lo != '' ) {
            $('#myboard').show();
            $('#logged').show();
            $('#loginli').hide();
            $('#logoutli').show();
            $('#user_name').text(lo);
        };
        if ( lo == '' ) {
            $('#logged').hide();
            $('#loginli').show();
            $('#logoutli').hide();
        };
        $("#btnlogin").click(function(){
            $("#sadas").show();
            $("#asdas").hide();
        });
    // });
</script>
<!-- Footer -->
<!-- 
<footer class='app-footer'>
</footer>
 -->
</body>
</html> 