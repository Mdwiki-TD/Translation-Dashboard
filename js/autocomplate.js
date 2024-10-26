
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

let cachedData = null; // متغير لتخزين البيانات مؤقتًا

$(".lang_input").autocomplete({
	source: function (request, response) {
		// إذا كانت البيانات مخزنة مسبقًا، نستخدمها مباشرةً
		if (cachedData) {
			const filteredData = filterData(cachedData, request.term);
			response(filteredData);
			return;
		}

		// طلب البيانات من الـ API مرة واحدة فقط عند أول استدعاء
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

// دالة لتصفية البيانات بناءً على المدخل
function filterData(data, term) {
	return $.map(data, function (item) {
		// مطابقة العنصر المدخل
		if (item) {
			if (item.code.toLowerCase().indexOf(term.toLowerCase()) === 0) {
				return item.code; //`${item.code} - ${item.name} (${item.autonym})`;
			}
		}
	});
}


