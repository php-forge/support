<?php

declare(strict_types=1);

namespace PHPForge\Support\Tests;

use PHPForge\Support\DirectoryCleaner;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Unit tests for {@see DirectoryCleaner} directory cleanup behavior.
 *
 * Verifies directory cleanup and error handling for {@see DirectoryCleaner::clean()}.
 *
 * Test coverage.
 * - Cleans directory contents recursively while preserving `.gitignore` and `.gitkeep`.
 * - Throws `RuntimeException` when the directory cannot be opened.
 *
 * {@see DirectoryCleaner} for implementation details.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class DirectoryCleanerTest extends TestCase
{
    public function testCleanRemovesAllFiles(): void
    {
        $dir = sys_get_temp_dir() . '/php-forge-support-' . bin2hex(random_bytes(8));
        mkdir($dir);

        try {
            mkdir("{$dir}/subdir");
            touch("{$dir}/test.txt");
            touch("{$dir}/subdir/test.txt");
            touch("{$dir}/.gitignore");
            touch("{$dir}/.gitkeep");

            DirectoryCleaner::clean($dir);

            self::assertFileDoesNotExist(
                "{$dir}/test.txt",
                "File 'test.txt' should not exist after 'clean' method is called.",
            );
            self::assertFileDoesNotExist(
                "{$dir}/subdir/test.txt",
                "File 'subdir/test.txt' should not exist after 'clean' method is called.",
            );
            self::assertFileExists(
                "{$dir}/.gitignore",
                "File '.gitignore' should remain after 'clean' method is called.",
            );
            self::assertFileExists(
                "{$dir}/.gitkeep",
                "File '.gitkeep' should remain after 'clean' method is called.",
            );
        } finally {
            @unlink("{$dir}/.gitignore");
            @unlink("{$dir}/.gitkeep");
            @rmdir($dir);
        }
    }

    public function testThrowRuntimeExceptionWhenCleanNonExistingDirectory(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to open directory: non-existing-directory');

        DirectoryCleaner::clean(__DIR__ . '/non-existing-directory');
    }
}
