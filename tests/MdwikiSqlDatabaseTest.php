<?php

declare(strict_types=1);

use APICalls\MdwikiSql\Database;
use PHPUnit\Framework\TestCase;

final class MdwikiSqlDatabaseTest extends TestCase
{
    private string $previousHome = '';

    protected function setUp(): void
    {
        $home = getenv('HOME');
        if ($home !== false) {
            $this->previousHome = $home;
        }
    }

    protected function tearDown(): void
    {
        if ($this->previousHome === '') {
            putenv('HOME');
        } else {
            putenv('HOME=' . $this->previousHome);
        }
    }

    public function testMissingConfigThrowsRuntimeException(): void
    {
        $tmpDir = sys_get_temp_dir() . '/mdwiki_sql_test_' . uniqid();
        mkdir($tmpDir, 0777, true);
        putenv('HOME=' . $tmpDir);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Database configuration error');

        try {
            new Database('localhost');
        } finally {
            $this->removeDir($tmpDir);
        }
    }

    private function removeDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = scandir($dir);
        if ($items === false) {
            return;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $this->removeDir($path);
            } else {
                @unlink($path);
            }
        }

        @rmdir($dir);
    }
}
