<?php

namespace Results\GetResults2026\Tables;

use function Results\ResultsTableHtml\make_table_start;

/**
 * Base class for all result tables.
 */
abstract class AbstractResultsTable
{
    protected function startTable(bool $isInProcess = false, bool $showInProgressButton = false): string
    {
        return make_table_start($isInProcess, $showInProgressButton);
    }

    protected function endTable(): string
    {
        return "</tbody></table>";
    }

    abstract public function render(array $items): string;
}
