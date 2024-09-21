
var api_end_point = document.location.origin + "/api.php?get=users";

// attach autocomplete behavior to input field
$(".td_user_input").autocomplete({
	source: function (request, response) {
		// make AJAX request to Wikipedia API
		$.ajax({
			url: api_end_point,
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
