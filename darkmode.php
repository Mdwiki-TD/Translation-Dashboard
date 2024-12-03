<!-- <link href='/helps/styles.css' rel='stylesheet' type='text/css'> -->
<!-- <link href="$hoste/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel='stylesheet' type='text/css'> -->

<script type="module" src="/Translation_Dashboard/js/color-modes.js"></script>
<?php
function dark_mode_icon()
{
    return <<<HTML
        <button class="btn btn-link nav-link py-2 px-0 px-lg-2 dropdown-toggle d-flex align-items-center" id="bd-theme" type="button" aria-expanded="false" data-bs-toggle="dropdown"
            data-bs-display="static" aria-label="Toggle theme (light)">
            <span class="theme-icon-active my-1">
                <i class="bi bi-sun-fill"></i>
            </span>
            <span class="d-lg-none ms-2" id="bd-theme-text"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="bd-theme-text">
            <li>
                <button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="light" aria-pressed="true">
                    <i class="bi bi-sun-fill me-2 opacity-50 theme-icon"></i> Light
                </button>
            </li>
            <li>
                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark" aria-pressed="false">
                    <i class="bi bi-moon-stars-fill me-2 opacity-50 theme-icon"></i> Dark
                </button>
            </li>
            <li>
                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="auto" aria-pressed="false">
                    <i class="bi bi-circle-half me-2 opacity-50 theme-icon"></i> Auto
                </button>
            </li>
        </ul>
    HTML;
}
