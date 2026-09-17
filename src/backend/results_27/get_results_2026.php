<?php

namespace Results\GetResults2026;

use Results\GetResults2026\Data\ResultsFetcher;

/**
 * Backward-compatible function.
 */
if (!function_exists('Results\GetResults2026\get_results_2026')) {
    function get_results_2026(string $cat, string $code, bool $debug = false): array
    {
        return (new ResultsFetcher($debug))->get($cat, $code);
    }
}
