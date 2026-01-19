<?php

declare(strict_types=1);

namespace PHPForge\Support;

use function str_replace;

/**
 * Line ending normalization helper for PHPUnit tests.
 *
 * Provides deterministic line ending normalization for cross-platform string assertions.
 *
 * Key features.
 * - Converts CRLF (`\r\n`) and CR (`\r`) line endings to LF (`\n`).
 * - Converts literal `\\n` sequences to LF (`\n`).
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class LineEndingNormalizer
{
    /**
     * Normalizes all line-ending formats to Unix LF (`\n`).
     *
     * Converts Windows CRLF (`\r\n`), old Mac CR (`\r`), and literal backslash-n sequences to actual newline characters
     * for consistent cross-platform processing.
     *
     * @param string $template Template string with potentially mixed line endings.
     *
     * @return string Template with normalized line endings.
     *
     * @infection-ignore-all Line ending normalization cannot be reliably mutation-tested on Windows due to
     * platform-specific behavior. Manual testing and integration tests on Unix/Linux CI environments provide coverage
     * for this logic.
     */
    public static function normalize(string $template): string
    {
        $template = str_replace(["\r\n", "\r"], "\n", $template);

        return str_replace('\\n', "\n", $template);
    }
}
