
function acceptCookieAlert() {
    document.cookie = "cookie_alert_dismissed1=true; max-age=31536000; path=/; Secure; SameSite=Lax";
}

function copy_target_text(id) {
    let textarea = document.getElementById(id);
    textarea.select();
    document.execCommand("copy");
}

$(document).ready(function () {
    // Initialize simple sortable tables
    $('.sortable').DataTable({
        stateSave: true,
        paging: false,
        info: false,
        searching: false
    });

    // Initialize paginated sortable tables
    $('.sortable2').DataTable({
        stateSave: true,
        lengthMenu: [
            [25, 50, 100, 200],
            [25, 50, 100, 200]
        ]
    });

    // Call get_views() function
    get_views();

    // Initialize Bootstrap tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(
        tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl)
    );

    // Initialize responsive tables with slight delay for DOM stability
    setTimeout(function () {
        $('.soro').DataTable({
            stateSave: true,
            lengthMenu: [
                [25, 50, 100, 200],
                [25, 50, 100, 200]
            ]
        });
    }, 200);


    // $('.card').CardWidget('toggle')
    $('.table_responsive').DataTable({
        stateSave: false,
        paging: false,
        info: false,
        searching: false,
        responsive: {
            details: true
            // display: $.fn.dataTable.Responsive.display.modal()
        }
    });

    $('.table_responsive_main').DataTable({
        stateSave: false,
        paging: false,
        info: false,
        searching: false,
        order: [
            [3, 'desc']
        ],
        responsive: {
            details: true
        }
    });

});
