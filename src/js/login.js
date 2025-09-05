
function login() {
    var cat = $('#cat').val() || '';
    var code = $('#code').val() || '';
    var type = $('input[name=type]:checked').val() || '';
    var test = $('#test').val() || '';
    // var url = 'https://mdwiki.toolforge.org/auth/index.php?a=login';
    var url = '/auth/index.php?a=login';
    // var url = 'login/index.php?doit=1';
    if (cat !== '') {
        url += '&cat=' + encodeURIComponent(cat);
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
