
function login() {
    var cat = $('#cat').val() || '';
    var code = $('#code').val() || '';
    var type = $('input[name=type]:checked').val() || '';
    var test = $('#test').val() || '';
    var url = 'auth.php?a=login&doit=1';
    // var url = 'login/index.php?doit=1';
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
