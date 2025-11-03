<?php

declare(strict_types=1);

use APICalls\MdwikiSql\Database;
use FilesystemIterator;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;

final class DatabaseConfigTest extends TestCase
{
    private ?string $originalHome = null;

    protected function setUp(): void
    {
        $this->originalHome = getenv('HOME') !== false ? getenv('HOME') : null;
    }

    protected function tearDown(): void
    {
        if ($this->originalHome !== null) {
            putenv('HOME=' . $this->originalHome);
        } else {
            putenv('HOME');
        }
    }

    public function testSetDbThrowsExceptionWhenConfigMissing(): void
    {
        $tempHome = sys_get_temp_dir() . '/dbconfig_test_missing_' . uniqid();
        $this->createTempHome($tempHome);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('could not be read or parsed');

        try {
            new Database('localhost');
        } finally {
            $this->cleanupTempHome($tempHome);
        }
    }

    public function testSetDbThrowsExceptionWhenPasswordMissing(): void
    {
        $tempHome = sys_get_temp_dir() . '/dbconfig_test_incomplete_' . uniqid();
        $this->createTempHome($tempHome, ['user' => 'exampleuser']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('password');

        try {
            new Database('tools');
        } finally {
            $this->cleanupTempHome($tempHome);
        }
    }

    private function createTempHome(string $path, array $config = []): void
    {
        $configDir = $path . '/confs';
        if (!is_dir($configDir)) {
            mkdir($configDir, 0777, true);
        }

        putenv('HOME=' . $path);

        if ($config !== []) {
            $ini = '';
            foreach ($config as $key => $value) {
                $ini .= sprintf("%s = \"%s\"\n", $key, $value);
            }
            file_put_contents($configDir . '/db.ini', $ini);
        }
    }

    private function cleanupTempHome(string $path): void
    {
        if (!is_dir($path)) {
            return;
        }

        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            if ($item->isDir()) {
                @rmdir($item->getPathname());
            } else {
                @unlink($item->getPathname());
            }
        }

        @rmdir($path);
    }
}
