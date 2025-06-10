
function add_it(item, data) {
	// ---
	var view = 0;
	var items = data.items;

	// get view count from items array
	items.forEach(function (aa) {
		view += aa['views'];
		// console.log(view);
	});
	//---
	item.text(view.toLocaleString());
	item.parent().attr('data-sort', view);
	//---
	var p = $('#hrefjsontoadd').text();
	p = p.replace(',', '');

	// add the view to hrefjsontoadd value
	var nu = parseFloat(p) + view;

	// format nu like 1,000
	$('#hrefjsontoadd').text(nu.toLocaleString());

}

function get_views() {
	// get the data from the data-json-url if the server not localhost
	if (window.location.hostname === 'localhost') {
		// log to console
		console.log('dont load get_views() in localhost');
		// return;
	}
	// ---
	$("[data-json-url]").each(function () {
		var item = $(this);
		var datajsonurl = item.attr("data-json-url");
		// get the data from the data-json-url then add it to the value
		// ---
		fetch(datajsonurl)
			.then(response => {
				if (!response.ok) {
					throw new Error('Network response was not ok');
				}
				return response.json();
			})
			.then(data => {
				add_it(item, data);
			})
			.catch(error => {
				console.log('error get_views()');
				console.error(error);
			});
		// ---
	});
};
