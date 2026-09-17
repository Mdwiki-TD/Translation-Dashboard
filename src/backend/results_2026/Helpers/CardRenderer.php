<?php

namespace Results\GetResults2026\Helpers;

function render(string $title, string $body, string $extraHeader = ''): string
{
    return <<<HTML
    <br>
    <div class='card'>
        <div class="card-header">
            <span class="card-title h5">
                $title
            </span>
            $extraHeader
            <div class="card-tools">
                <button type="button" class="btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
            </div>
        </div>
        <div class='card-body1 card2'>
            $body
        </div>
    </div>
    HTML;
}
