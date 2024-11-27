
// https://mdwiki.toolforge.org/api.php
var api_end_point = document.location.origin + "/api.php";

// attach autocomplete behavior to input field
$(".td_user_input").autocomplete({
	source: function (request, response) {
		// make AJAX request to Wikipedia API
		$.ajax({
			url: api_end_point + "?get=users",
			dataType: "json",
			data: {
				userlike: request.term
			},
			success: function (data) {
				// extract titles from API response and pass to autocomplete
				response($.map(data.results, function (item) {
					return item.username
				}));
			}
		});
	}
});

let cachedData = null; // Variable for temporary data storage

$(".lang_input").autocomplete({
	source: function (request, response) {
		// If data is already cached, use it directly
		if (cachedData) {
			const filteredData = filterData(cachedData, request.term);
			response(filteredData);
			return;
		}

		// Fetch data from API only once on first call
		$.ajax({
			url: api_end_point + "?get=lang_names",
			dataType: "json",
			success: function (data) {
				cachedData = data.results; // تخزين البيانات مؤقتًا
				const filteredData = filterData(cachedData, request.term);
				response(filteredData);
			}
		});
	}
});

function filterData(data, term) {
	return $.map(data, function (item) {
		if (item) {
			if (item.code.toLowerCase().indexOf(term.toLowerCase()) === 0) {
				return {
					label: `${item.code} - ${item.name} (${item.autonym})`,
					value: item.code
				};
			}
		}
	});
}


