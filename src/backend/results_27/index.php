<?php

namespace Results\GetResults2026;

/**
 * Public entry point – keeps the old function name.
 */
if (!function_exists('Results\GetResults2026\results_loader_2026')) {
    function results_loader_2026(array $data): string
    {
        return (new ResultsLoader())->load($data);
    }
}
