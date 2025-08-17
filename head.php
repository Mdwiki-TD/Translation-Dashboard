<!DOCTYPE html>
<?php
if (isset($_REQUEST["test"])) {
    ini_set("display_errors", 1);
    ini_set("display_startup_errors", 1);
    error_reporting(E_ALL);
}

function get_host()
{
    // $hoste = get_host();
    //---
    static $cached_host = null;
    //---
    if ($cached_host !== null) {
        return $cached_host; // استخدم القيمة المحفوظة
    }
    //---
    $hoste = ($_SERVER["SERVER_NAME"] == "localhost")
        ? "https://cdnjs.cloudflare.com"
        : "https://tools-static.wmflabs.org/cdnjs";
    //---
    if ($hoste == "https://tools-static.wmflabs.org/cdnjs") {
        $url = "https://tools-static.wmflabs.org";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true); // لا نريد تحميل الجسم
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // لمنع الطباعة

        curl_setopt($ch, CURLOPT_TIMEOUT, 3); // المهلة القصوى للاتصال
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; CDN-Checker)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

        $result = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        // إذا فشل الاتصال أو لم تكن الاستجابة ضمن 200–399، نستخدم cdnjs
        if ($result === false || !empty($curlError) || $httpCode < 200 || $httpCode >= 400) {
            $hoste = "https://cdnjs.cloudflare.com";
        }
    }

    $cached_host = $hoste;

    return $hoste;
}

$hoste = get_host();

$stylesheets = [
    "/Translation_Dashboard/css/styles.css",
    "/Translation_Dashboard/css/dashboard_new1.css",
    "/Translation_Dashboard/css/sidebars.css",
    "$hoste/ajax/libs/font-awesome/5.15.3/css/all.min.css",
    "$hoste/ajax/libs/bootstrap/5.3.7/css/bootstrap.min.css",
    "$hoste/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css",
    "$hoste/ajax/libs/bootstrap-select/1.14.0-beta3/css/bootstrap-select.css",
    "$hoste/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css",

    // "$hoste/ajax/libs/datatables.net-bs5/1.13.5/dataTables.bootstrap5.css",
    "$hoste/ajax/libs/datatables.net-bs5/2.2.2/dataTables.bootstrap5.css",
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
    "$hoste/ajax/libs/datatables.net/2.2.2/dataTables.js",
    "$hoste/ajax/libs/datatables.net-bs5/2.2.2/dataTables.bootstrap5.min.js",

    // "$hoste/ajax/libs/datatables.net-fixedheader/3.4.0/dataTables.fixedHeader.min.js",
    "$hoste/ajax/libs/datatables-responsive/3.0.4/dataTables.responsive.js",

    "/Translation_Dashboard/js/to.js",
    "/Translation_Dashboard/js/login.js",
    "/td/plugins/chart.js/Chart.min.js",
    "/Translation_Dashboard/js/g.js",
    "/Translation_Dashboard/js/theme.js",
];

$scripts_module = [
    "/Translation_Dashboard/js/color-modes.js",
];


function head()
{
    global $stylesheets, $scripts, $scripts_module;
    // ---
    foreach ($stylesheets as $css) {
        echo "\n\t<link rel='stylesheet' href='" . $css . "'>";
    }
    foreach ($scripts as $js) {
        echo "\n\t<script src='" . $js . "'></script>";
    }
    foreach ($scripts_module as $js) {
        echo "\n\t<script type='module' src='" . $js . "'></script>";
    }
    echo "\n";
}
?>

<html lang="en" dir="ltr" data-bs-theme="light" xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <meta name="theme-color" content="#111111" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#eeeeee" media="(prefers-color-scheme: dark)">
    <title>Wiki Project Med Translation Dashboard</title>

    <?php
    if (!isset($_GET["noboot"])) {
        head();
    };
    ?>
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

        .Dropdown_menu_toggle {
            display: none;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .Dropdown_menu_toggle {
                display: block;
            }

            .div_menu {
                display: none;
                flex-direction: column;
                /* width: 100%; */
                /* background: #ddddff; */
                padding: 0;
                border-radius: 5px;
            }

            .div_menu.mactive {
                display: block;
            }
        }
    </style>

</head>
