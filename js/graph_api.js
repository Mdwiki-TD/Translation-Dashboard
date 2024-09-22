
function graph_js_params(id, params) {
    var end_point = window.location.origin
    var url = end_point + "/api.php?get=status&" + $.param(params);
    console.log(url)
    fetch(url)
        .then(response => response.json())
        .then(data => {
            var results = data.results
            // { "date": "2022-01", "count": "1" }, { "date": "2022-02", "count": "1" }, ....

            var labels = []
            var dat = []

            for (var i = 0; i < results.length; i++) {
                labels.push(results[i].date)
                dat.push(results[i].count)
            }

            console.log(JSON.stringify(labels))
            console.log(JSON.stringify(dat))

            graph_js(labels, dat, id)
        })
}

