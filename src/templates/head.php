<?php

namespace TDC\Head;

$hoste = (getenv('APP_ENV') === 'production')
    ? "https://tools-static.wmflabs.org/cdnjs"
    : "https://cdnjs.cloudflare.com";

$stylesheets = [
    "/Translation_Dashboard/css/styles.css",
    "/Translation_Dashboard/css/dashboard_new1.css",
    // "/Translation_Dashboard/css/sidebars.css",
    "$hoste/ajax/libs/font-awesome/5.15.3/css/all.min.css",
    "$hoste/ajax/libs/bootstrap/5.3.7/css/bootstrap.min.css",
    "$hoste/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css",
    "$hoste/ajax/libs/bootstrap-select/1.14.0-beta3/css/bootstrap-select.min.css",
    "$hoste/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css",

    // "$hoste/ajax/libs/datatables.net-bs5/1.13.5/dataTables.bootstrap5.min.css",
    "$hoste/ajax/libs/datatables.net-bs5/2.3.4/dataTables.bootstrap5.min.css",

    "$hoste/ajax/libs/datatables.net-responsive-bs5/3.0.4/responsive.bootstrap5.min.css",

    "/Translation_Dashboard/css/mobile_format.css",
    "/Translation_Dashboard/css/Responsive_Table.css",
    "/Translation_Dashboard/css/theme.css",
];

$scripts = [
    "$hoste/ajax/libs/jquery/3.7.0/jquery.min.js",
    "$hoste/ajax/libs/popper.js/2.11.8/umd/popper.min.js",
    "$hoste/ajax/libs/bootstrap/5.3.7/js/bootstrap.min.js",
    "$hoste/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js",
    "$hoste/ajax/libs/bootstrap-select/1.14.0-beta3/js/bootstrap-select.min.js",

    // "$hoste/ajax/libs/datatables.net/2.1.1/jquery.dataTables.min.js",
    // "$hoste/ajax/libs/datatables.net-bs5/1.13.5/dataTables.bootstrap5.min.js",
    "$hoste/ajax/libs/datatables.net/2.3.4/dataTables.min.js",
    "$hoste/ajax/libs/datatables.net-bs5/2.3.4/dataTables.bootstrap5.min.js",

    // "$hoste/ajax/libs/datatables.net-fixedheader/3.4.0/dataTables.fixedHeader.min.js",
    "$hoste/ajax/libs/datatables-responsive/3.0.4/dataTables.responsive.min.js",

    "/Translation_Dashboard/js/to.js",
    "/td/plugins/chart.js/Chart.min.js",
    "/Translation_Dashboard/js/g.js",
    "/Translation_Dashboard/js/theme.js",
];

$scripts_module = [
    "/Translation_Dashboard/js/color-modes.js",
];

function head(): string
{
    global $stylesheets, $scripts, $scripts_module;

    $text = "";

    foreach ($stylesheets as $css) {
        $text .= "\n\t<link rel='stylesheet' href='" . $css . "'>";
    }
    foreach ($scripts as $js) {
        $text .= "\n\t<script src='" . $js . "'></script>";
    }
    foreach ($scripts_module as $js) {
        $text .= "\n\t<script type='module' src='" . $js . "'></script>";
    }
    $text .= "\n";
    return $text;
}

/**
 * Generates the full HTML head section, including meta tags, title, and linked resources.
 *
 * @return string The complete HTML head section as a string.
 */
function print_full_head(): string
{
    $head_text = head();

    $full_head = <<<HTML
        <html lang="en" dir="ltr" data-bs-theme="light" xmlns="http://www.w3.org/1999/xhtml">

        <head>
            <meta charset="UTF-8">
            <meta name="robots" content="noindex">
            <link rel="icon" href="/favicon.svg" sizes="any">
            <link rel="icon" href="/favicon.svg" type="image/svg+xml">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <meta name="color-scheme" content="light dark">
            <meta name="theme-color" content="#111111" media="(prefers-color-scheme: light)">
            <meta name="theme-color" content="#eeeeee" media="(prefers-color-scheme: dark)">
            <title>Wiki Project Med Translation Dashboard</title>
            $head_text
            <style>
                .table_text_left>tbody>tr>th,
                .table_text_left>tbody>tr>td,
                .table_text_left>thead>tr>td,
                .table_text_left>thead>tr>th {
                    text-align: left !important;
                }

                .leaderboard_tables>tbody>tr>td,
                .leaderboard_tables>tbody>tr>th,
                .leaderboard_tables>thead>tr>td,
                .leaderboard_tables>thead>tr>th {
                    padding: 6px;
                    line-height: 1.42857143;
                    vertical-align: top;
                    border-top: 1px solid #ddd;
                }

                a {
                    text-decoration: none;
                    word-break: break-all !important;
                }
                .logo-text {
                    background: linear-gradient(45deg, #6b8cff, #8b9fff);
                    -webkit-background-clip: text;
                    background-clip: text;
                    -webkit-text-fill-color: transparent;
                    transition: opacity 0.3s ease;
                }
            </style>
        </head>
    HTML;

    return $full_head;
}


function is_active($url)
{
    $file_name = basename($_SERVER['PHP_SELF']);
    // echo "file_name: $file_name <br>";

    if ($file_name == $url) {
        return 'active';
    }

    return '';
}

function write_body(string $coord_tools, string $li_user): string
{
    $leaderboard_active = is_active('leaderboard.php');
    $missing_active = is_active('missing.php');

    return <<<HTML
        <header class="mb-3 border-bottom">
            <nav class="navbar navbar-expand-lg bg-body-tertiary shadow" id="mainnav">
                <div class="container-fluid" id="navbardiv">
                    <a class="navbar-brand mb-0 h1" href="/Translation_Dashboard/index.php" style="color:#0d6efd;">
                        <img class='med-logo' width="40px" height="40px" src='/favicon.svg' decoding='async' alt='Wiki Project Med Foundation logo'>
                        <span class='d-none d-md-inline tool_title'>WikiProjectMed Translation Dashboard</span>
                        <span class='d-inline d-md-none tool_title'>WikiProjectMed TD</span>
                    </a>

                    <div class="d-flex align-items-center order-lg-last">
                        <button class="navbar-toggler me_ms_by_dir" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar"
                            aria-controls="collapsibleNavbar" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <button class="theme-toggle btn btn-link me-ms-auto" aria-label="Toggle theme">
                            <i class="bi bi-moon-stars-fill"></i>
                        </button>
                    </div>
                    <div class="collapse navbar-collapse" id="collapsibleNavbar">
                        <ul class="navbar-nav flex-row flex-wrap bd-navbar-nav">
                            <li class="nav-item col-lg-auto col-md-4 col-sm-6 col-6 {$leaderboard_active}" id="leaderboard">
                                <a class="nav-link py-2 px-0 px-lg-2" href="/Translation_Dashboard/leaderboard.php">
                                    <span class="navtitles"> <i class="bi bi-bar-chart-line me-1"></i> Leaderboard</span>
                                </a>
                            </li>
                            <li class="nav-item col-lg-auto col-md-4 col-sm-6 col-6" id="Prior">
                                <a class="nav-link py-2 px-0 px-lg-2" target="_blank" href="/prior">
                                    <span class="navtitles">
                                        <i class="bi bi-bar-chart me-1"></i> Prior
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item col-lg-auto col-md-4 col-sm-6 col-6 {$missing_active}" id="missing">
                                <a class="nav-link py-2 px-0 px-lg-2" href="/Translation_Dashboard/missing.php">
                                    <span class="navtitles">
                                        <i class="bi bi-card-list me-1"></i> Missing
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item col-lg-auto col-md-4 col-sm-6 col-6" id="coord">
                                $coord_tools
                            </li>

                            <li class="nav-item col-lg-auto col-md-4 col-sm-6 col-6">
                                <a class="nav-link py-2 px-0 px-lg-2" href="https://github.com/MrIbrahem/Translation-Dashboard" target="_blank">
                                    <span class="navtitles">
                                        <i class="bi bi-github me-1"></i> Github
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item col-lg-auto col-md-4 col-sm-6 col-6">
                                <span class="nav-link py-2 px-0 px-lg-2" id="load_time"></span>
                            </li>
                        </ul>
                        <hr class="d-lg-none text-dark-subtle text-50">
                        <ul class="navbar-nav flex-row flex-wrap bd-navbar-nav ms-lg-auto">
                            $li_user
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- Logout Modal-->
            <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6 class="modal-title" id="exampleModalLabel">Ready to Leave?</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">Select &quot;Logout&quot; below if you are ready to end your current session.</div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                            <a class="btn btn-outline-primary" href="/auth/logout.php">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        HTML;
}
