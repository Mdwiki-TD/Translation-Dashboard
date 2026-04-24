
function login() {
    var camp = $('#camp').val() || '';
    var code = $('#code').val() || '';
    var type = $('input[name=type]:checked').val() || '';
    var test = $('#test').val() || '';
    // var url = 'https://mdwiki.toolforge.org/auth/index.php?a=login';
    var url = '/auth/index.php?a=login';
    // var url = 'login/index.php?doit=1';
    if (camp !== '') {
        url += '&camp=' + encodeURIComponent(camp);
    }
    if (code !== '') {
        url += '&code=' + encodeURIComponent(code);
    }
    if (type !== '') {
        url += '&type=' + encodeURIComponent(type);
    }
    if (test !== '') {
        url += '&test=' + encodeURIComponent(test);
    }

    window.location.href = url;
}
