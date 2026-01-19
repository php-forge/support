<?php

declare(strict_types=1);

namespace PHPForge\Support;

use PHPForge\Support\Exception\Message;
use RuntimeException;

use function basename;
use function closedir;
use function is_dir;
use function opendir;
use function readdir;
use function rmdir;
use function unlink;

/**
 * Filesystem cleanup utilities for PHPUnit tests.
 *
 * Provides helper methods to remove directory contents while preserving `.gitignore` and `.gitkeep`.
 *
 * Key features.
 * - Preserves `.gitignore` and `.gitkeep` during cleanup.
 * - Removes directory contents recursively via {@see DirectoryCleaner::clean()}.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class DirectoryCleaner
{
    /**
     * Removes directory contents recursively while preserving `.gitignore` and `.gitkeep`.
     *
     * Iterates through `$basePath` and removes all files and subdirectories except `.`, `..`, `.gitignore`, and
     * `.gitkeep`. Subdirectories are processed recursively before removal.
     *
     * File and directory removals are attempted with error suppression.
     *
     * @param string $basePath Absolute path to the directory whose contents will be removed.
     *
     * @throws RuntimeException if the directory cannot be opened for reading.
     */
    public static function clean(string $basePath): void
    {
        $handle = @opendir($basePath);

        if ($handle === false) {
            $dirname = basename($basePath);
            throw new RuntimeException(
                Message::UNABLE_TO_OPEN_DIRECTORY->getMessage($dirname),
            );
        }

        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            if ($file === '.gitignore' || $file === '.gitkeep') {
                continue;
            }

            $path = $basePath . DIRECTORY_SEPARATOR . $file;

            if (is_dir($path)) {
                self::clean($path);
                @rmdir($path);
            } else {
                @unlink($path);
            }
        }

        closedir($handle);
    }
}
