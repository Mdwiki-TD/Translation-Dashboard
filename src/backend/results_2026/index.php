<?php

namespace Results\GetResults2026;

/**
 * Public entry point – keeps the old function name.
 */
function results_loader_2026(array $data): string
{
    return (new ResultsLoader())->load($data);
}
