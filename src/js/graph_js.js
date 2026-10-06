
/**
 * @param {string | any[]} _labels
 * @param {null[]} _data
 * @param {string} _id
 */
function graph_js(_labels, _data, _id) {
    if (_data.length == 0) {
        console.log("graph_js: No data")
        return
    }
    if (_labels.length == 0) {
        console.log("graph_js: No labels")
        return
    }
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
    // var colors = new Array(len).fill('#007bff').concat(new Array(2).fill('#bcbcbc'));

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
    // @ts-ignore
    var visitorsChart = $("#" + _id);

    // create Chart
    // @ts-ignore
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

/**
 * @param {string} chartdata
 * @param {string} _id
 */
function render_graph(chartdata, _id) {
    let chart = JSON.parse(chartdata);
    graph_js(chart.labels, chart.counts, _id)
}
