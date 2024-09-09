/**
     * Retrieves data from specified JSON endpoints and updates the DOM elements accordingly.
     * The function checks if the current hostname is 'localhost'; if so, it logs a message and exits early.
     * For each element with the attribute 'hrefjson', it performs an AJAX GET request to the specified URL,
     * aggregates view counts from the returned data, and updates the text of the element as well as its parent.
     * Additionally, it updates a separate element with the cumulative view count.
     *
     * @function to_get
     * @returns {boolean} Returns true if the function executes successfully.
     *
     * @throws {Error} Throws an error if the AJAX request fails, but does not handle it explicitly.
     *
     * @example
     * // Call the function to initiate data retrieval and DOM updates
     * to_get();
     */
function to_get() {
	// get the data from the hrefjson if the server not localhost
	if (window.location.hostname === 'localhost') {
		// log to console
		console.log('dont load to_get() in localhost');
		return;
	}
	var ele = $("[hrefjson]");
	ele.each(function () {
		var item = $(this);
		var hrefjson = item.attr("hrefjson");
		// get the data from the hrefjson then add it to the value
		//---------------
		// console.log(hrefjson);

		// console.log(item.text());
		//---------------
		jQuery.ajax({
			url: hrefjson,
			// data: params,
			type: 'GET',
			success: function (data) {
				//---------------
				var view = 0;
				var items = data.items;
				// get view count from items array
				items.forEach(function (aa) {
					view += aa['views'];
					// console.log(view);
				});
				//---
				item.text(view);
				var pa = item.parent();
				pa.attr('data-sort', view);
				//---
				// var txt2 = $("<span></span>").text(view).hide();     // Create with jQuery
				// item.before(txt2);
				//---
				var p = $('#hrefjsontoadd').text();
				// add the view to hrefjsontoadd value
				var nu = parseFloat(p) + view;
				$('#hrefjsontoadd').text(nu);
				//---
			},
			error: function (data) {
			}
		});
	});
	//---
	return true;
};
