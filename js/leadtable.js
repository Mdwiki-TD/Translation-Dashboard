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

		if ($(this).text() !== '') {
			var isStatusColumn = (($(this).text() == 'Status') ? true : false);
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
			if (!isStatusColumn) {
				var statusItems = [];

				/* ### IS THERE A BETTER/SIMPLER WAY TO GET A UNIQUE ARRAY OF <TD> data-filter ATTRIBUTES? ### */
				table.column(i).nodes().to$().each(function (d, j) {
					var thisStatus = $(j).attr("data-filter");

					if ($.inArray(thisStatus, statusItems) === -1) statusItems.push(thisStatus);
				});

				statusItems.sort();

				$.each(statusItems, function (i, item) {
					select.append('<option value="' + item + '">' + item + '</option>');
				});

			}
			// All other non-Status columns (like the example)
			else {
				table.column(i).data().unique().sort().each(function (d, j) {
					select.append('<option value="' + d + '">' + d + '</option>');
				});
			}

		}
	});


}

function leadtable1() {
    // Setup - add a text input to each footer cell
    $('#leadtable thead tr')
        .clone(true)
        .addClass('filters')
        .appendTo('#leadtable thead');

    var table = $('#leadtable').DataTable({
        lengthMenu: [
            [25, 50, 100, 200],
            [25, 50, 100, 200]
        ],
        orderCellsTop: true,
        fixedHeader: true,
        initComplete: function () {
            var api = this.api();

            // For each column
            api
                .columns()
                .eq(0)
                .each(function (colIdx) {
                    // Set the header cell to contain the input element
                    var cell = $('.filters th').eq(
                        $(api.column(colIdx).header()).index()
                    );
                    var title = $(cell).text();
                    var titles_to_skip = ['Campaign', 'Date'];
                    if (!titles_to_skip.includes(title)) {
                        $(cell).html('');
                        return;
                    }

                    // var html = $(cell).html() + '<input type="text" placeholder="' + title + '" />';
                    var html = '<input type="text" placeholder="' + title + '" />';
                    // skip if title == #
                    $(cell).html(html);

                    // On every keypress in this input
                    $(
                        'input',
                        $('.filters th').eq($(api.column(colIdx).header()).index())
                    )
                        .off('keyup change')
                        .on('change', function (e) {
                            // Get the search value
                            $(this).attr('title', $(this).val());
                            var regexr = '({search})'; //$(this).parents('th').find('select').val();

                            var cursorPosition = this.selectionStart;
                            // Search the column for that value
                            api
                                .column(colIdx)
                                .search(
                                    this.value != ''
                                        ? regexr.replace('{search}', '(((' + this.value + ')))')
                                        : '',
                                    this.value != '',
                                    this.value == ''
                                )
                                .draw();
                        })
                        .on('keyup', function (e) {
                            e.stopPropagation();

                            $(this).trigger('change');
                            $(this)
                                .focus()[0]
                                .setSelectionRange(cursorPosition, cursorPosition);
                        });

                });
        },
    });
}

$(document).ready(function () {
    leadtable();
    // leadtable1();
});