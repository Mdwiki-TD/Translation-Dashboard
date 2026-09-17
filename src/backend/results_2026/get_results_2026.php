<?php

namespace Results\GetResults2026;

use Results\GetResults2026\Data\ResultsFetcher;

/**
 * Backward-compatible function.
 */
function get(string $cat, string $code, bool $debug = false): array
{
    return (new ResultsFetcher($debug))->get($cat, $code);
}
