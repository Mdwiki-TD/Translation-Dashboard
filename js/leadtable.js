
function leadtable() {

    $('#leadtable thead tr')
        .clone(true)
        .appendTo('#leadtable tfoot');

    var table = $('#leadtable').DataTable({
        "oLanguage": {
            "sInfo": "Showing _START_ to _END_ of _TOTAL_ items."
        }
    });

    $("#leadtable tfoot th").each(function (i) {

        var titles_to_skip = ['Campaign', 'Date'];
        if (!titles_to_skip.includes($(this).text())) {
            return;
        }
        var select = $('<select><option value=""></option></select>')
            .appendTo($(this).empty())
            .on('change', function () {
                var val = $(this).val();

                table.column(i)
                    .search(val ? '^' + $(this).val() + '$' : val, true, false)
                    .draw();
            });

        // Get the Status values a specific way since the status is a anchor/image
        var statusItems = [];

        /* ### IS THERE A BETTER/SIMPLER WAY TO GET A UNIQUE ARRAY OF <TD> data-filter ATTRIBUTES? ### */
        table.column(i).nodes().to$().each(function (d, j) {
            var thisStatus = $(j).attr("data-filter");
            // split thisStatus by ,

            if ($.inArray(thisStatus, statusItems) === -1) statusItems.push(thisStatus);
        });

        statusItems.sort();

        $.each(statusItems, function (i, item) {
            select.append('<option value="' + item + '">' + item + '</option>');
        });

    });
}

$(document).ready(function () {
    leadtable();
});