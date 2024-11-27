
function graph_js(_labels, _data, _id) {

    var ticksStyle = {
        fontColor: '#495057',
        fontStyle: 'bold'
    }

    var mode = "index"
    var intersect = true

    var _data2 = _data.slice()
    // remove last element in _data
    _data.pop()
    _data.push(null)


    // replace last element in _data by null
    // Assuming _data is an array variable
    for (let i = 0; i < _data.length - 2; i++) {
        _data2[i] = null;
    }

    var len = _labels.length - 2
    var colors = new Array(len).fill('#007bff').concat(new Array(2).fill('#bcbcbc'));

    var areaChartOptions = {
        title: {
            display: false,
            text: 'Translation by month',
        },

        maintainAspectRatio: false,

        tooltips: {
            mode: mode,
            intersect: intersect
        },
        hover: {
            mode: mode,
            intersect: intersect
        },
        legend: {
            display: false
        },
        scales: {
            yAxes: [{
                display: true,
                gridLines: {
                    display: true,
                },
                ticks: ticksStyle
            }],
            xAxes: [{
                display: true,
                gridLines: {
                    display: false
                },
                ticks: ticksStyle
            }]
        },
        elements: {
            line: {
                borderDash: [0, 0]
            }
        },
    }
    var visitorsChart = $("#" + _id);
    // إنشاء الرسم البياني
    var myChart = new Chart(visitorsChart, {
        data: {
            labels: _labels,
            datasets: [{
                type: "line",
                data: _data,
                backgroundColor: "transparent",
                borderColor: '#007bff',
                pointBorderColor: "#007bff",
                pointBackgroundColor: "#007bff",
                pointRadius: 3,
                fill: false
            }, {
                type: "line",
                data: _data2,
                backgroundColor: "transparent",
                borderColor: '#bcbcbc',
                pointBorderColor: "#007bff",
                pointBackgroundColor: "#007bff",
                pointRadius: 3,
                fill: false
            }]
        },
        options: areaChartOptions
    })
}


function graph_js_params(id, params) {
    var end_point = window.location.origin
    fetch(end_point + "/api.php??get=status&" + $.param(params))
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

            graph_js(labels, dat, id)
        })
}

