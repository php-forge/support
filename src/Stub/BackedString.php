<?php

declare(strict_types=1);

namespace PHPForge\Support\Stub;

/**
 * Stub enum for backed string values.
 *
 * Provides deterministic values required by the test suite.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
enum BackedString: string
{
    /**
     * Type representing a value.
     */
    case VALUE = 'value';
}
