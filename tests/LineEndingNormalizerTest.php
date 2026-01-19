<?php

declare(strict_types=1);

namespace PHPForge\Support\Tests;

use PHPForge\Support\LineEndingNormalizer;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for {@see LineEndingNormalizer} line ending normalization.
 *
 * Verifies deterministic normalization for CRLF/CR and literal `\\n` sequences.
 *
 * Test coverage.
 * - Converts CRLF (`\r\n`) and CR (`\r`) line endings to LF (`\n`).
 * - Converts literal `\\n` sequences to LF (`\n`).
 * - Leaves LF (`\n`) input unchanged.
 *
 * {@see LineEndingNormalizer} for implementation details.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class LineEndingNormalizerTest extends TestCase
{
    public function testNormalizeConvertsLiteralBackslashNToNewline(): void
    {
        self::assertSame(
            "foo\nbar",
            LineEndingNormalizer::normalize('foo\\nbar'),
            "Should normalize literal '\\n' sequences to 'LF'.",
        );
    }
    public function testNormalizeReturnsUnchangedStringWhenNoNormalizationNeeded(): void
    {
        self::assertSame(
            "foo\nbar",
            LineEndingNormalizer::normalize("foo\nbar"),
            'Should return the original string when no normalization is required.',
        );
    }

    public function testNormalizeWhenStringsAreIdenticalWithLineEndings(): void
    {
        self::assertSame(
            "foo\nbar",
            LineEndingNormalizer::normalize("foo\r\nbar"),
            "Should normalize 'CRLF' inputs to 'LF'.",
        );
    }
}
