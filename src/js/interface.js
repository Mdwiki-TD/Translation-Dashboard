
$(document).ready(function () {
    var tt = new ToolTranslation({
        tool: 'wiki_project_med_dashboard',
        language: "en",
        fallback: "en",
        highlight_missing: true,
        callback: function () {
            tt.addILdropdown($('#interface_language_wrapper'));
        }
    });
    // ---
    // wait 1 second before doing next
    setTimeout(function () {
        // $('#interface_language_wrapper>form').addClass('w-50');
        $('#interface_language_wrapper>form>a').hide();
        // <option value="ar" selected="">AR</option> change AR to "العربية"
        $('option[value="ar"]').text('العربية');
    }, 1000);
});
