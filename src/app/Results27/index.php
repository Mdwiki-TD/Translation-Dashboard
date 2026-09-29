<?php

namespace App\Results27;

/**
 * Public entry point – keeps the old function name.
 */
function results_loader_27(array $data): string
{
    return (new ResultsLoader())->load($data);
}
