<?php

declare(strict_types=1);

namespace PHPForge\Support\Tests\Stub;

/**
 * Stub for a nacked enum.
 *
 * Provides deterministic integer-backed values required by the test suite.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
enum TestIntBackedEnum: int
{
    case Bar = 1;
    case Foo = 2;
}
