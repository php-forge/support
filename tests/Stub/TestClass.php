<?php

declare(strict_types=1);

namespace PHPForge\Support\Tests\Stub;

/**
 * Stub for a concrete test class extending TestBaseClass for inheritance and visibility testing.
 *
 * Provides a private property and a protected method to verify property and method accessibility in child classes.
 *
 * @copyright Copyright (C) 2025 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class TestClass extends TestBaseClass
{
    private string $property = 'value';

    protected function inaccessibleMethod(): string
    {
        return $this->property;
    }
}
