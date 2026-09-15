<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;

use function SQLorAPI\Process\delete_in_process_entry;

class ProcessDataTest extends TestCase
{
    public function testDeleteInProcessEntryEmptyParamsReturnsFalse(): void
    {
        $this->assertFalse(delete_in_process_entry('', 'Test Title', 'ar'));
        $this->assertFalse(delete_in_process_entry('TestUser', '', 'ar'));
        $this->assertFalse(delete_in_process_entry('TestUser', 'Test Title', ''));
    }
}
