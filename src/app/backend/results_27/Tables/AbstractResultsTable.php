<?php

namespace App\Results\GetResults27\Tables;

use function App\Utils\ResultsTableHtml\make_table_start;

/**
 * Base class for all result tables.
 */
abstract class AbstractResultsTable
{
    protected function startTable(bool $isInProcess = false): string
    {
        return make_table_start($isInProcess);
    }

    protected function endTable(): string
    {
        return "</tbody></table>";
    }

    abstract public function render(array $items): string;
}
