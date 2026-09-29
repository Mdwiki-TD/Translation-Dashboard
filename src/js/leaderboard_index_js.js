
async function get_categories() {
    const campaign_to_categories = {};
    const response = await fetch('/api.php?get=categories');
    const data = await response.json();

    data.results.forEach(item => {
        campaign_to_categories[item.campaign] = item.category;
    });
    return campaign_to_categories;
}

async function renderJsLeaderboard() {
    $('.sortable').DataTable({
        stateSave: false,
        paging: false,
        info: false,
        searching: false
    });

    const campaign_to_categories = await get_categories();

    function getFormData(d) {
        // d['get'] = 'top_users';

        // '/api.php?get=top_users&year=&user_group=&cat=';

        const formData = $('#leaderboard_filter').serializeArray();
        formData.forEach(field => {
            if (field.value.trim()) {
                d[field.name] = field.value;
            }
        });

        d["cat"] = campaign_to_categories[d["camp"]] ?? '';

        return d;
    }
    $('#Topusers').DataTable({
        stateSave: false,
        paging: false,
        info: false,
        searching: false,
        ajax: {
            url: '/api.php?get=top_users',
            data: getFormData,
            dataSrc: function (json) {
                // احتساب الإجماليات
                let totalUsers = json.results.length;
                let totalTargets = 0;
                let totalWords = 0;
                let totalViews = 0;

                json.results.forEach(function (row) {
                    totalTargets += Number(row.targets) || 0;
                    totalWords += Number(row.words) || 0;
                    totalViews += Number(row.views) || 0;
                });

                // تحديث عناصر HTML
                $('#c_user').text(totalUsers.toLocaleString());
                $('#c_articles').text(totalTargets.toLocaleString());
                $('#c_words').text(totalWords.toLocaleString());
                $('#c_pv').text(totalViews.toLocaleString());

                return json.results;
            }
        },
        columns: [{ // رقم تسلسلي تلقائي
            data: null,
            title: '#',
            render: function (data, type, row, meta) {
                return meta.row + 1;
            },
            className: 'dt-center'
        },
        {
            data: 'user',
            title: 'User',
            render: function (data, type) {
                return `<a href="/Translation_Dashboard/leaderboard.php?get=users&user=${data}">${data}</a>`;
            }
        },
        {
            data: 'targets',
            title: 'Number',
            render: function (data) {
                return Number(data).toLocaleString();
            }
        },
        {
            data: 'words',
            title: 'Words',
            render: function (data) {
                return Number(data).toLocaleString();
            }
        },
        {
            data: 'views',
            title: 'Pageviews',
            render: function (data) {
                return Number(data).toLocaleString();
            }
        }
        ]
    });

    graph_js_params('chart09', getFormData({}))

    $('#Toplangs').DataTable({
        stateSave: false,
        // order: [ [2, 'desc'] ],
        paging: false,
        info: false,
        searching: false,
        ajax: {
            url: '/api.php?get=top_langs',
            data: getFormData,
            dataSrc: function (json) {
                let total = json.results.length;
                $('#c_lang').text(total);
                return json.results;
            }
        },
        columns: [{ // رقم تسلسلي تلقائي
            data: null,
            title: '#',
            render: function (data, type, row, meta) {
                return meta.row + 1;
            },
            className: 'dt-center'
        },
        {
            data: 'lang',
            title: 'Language',
            render: function (data, type, row, meta) {
                return `<a href="/Translation_Dashboard/leaderboard.php?get=langs&langcode=${data}">${row.lang_name}</a>`;
            }
        },
        {
            data: 'targets',
            title: 'Count',
            render: function (data) {
                return Number(data).toLocaleString();
            }
        },
        // { data: 'words', visible: false },
        {
            data: 'views',
            title: 'Pageviews',
            render: function (data) {
                return Number(data).toLocaleString();
            }
        }
        ]
    });

}
