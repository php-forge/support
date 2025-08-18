<?php

declare(strict_types=1);

namespace PHPForge\Support\Tests\Stub;

/**
 * Abstract base class providing a protected method and private property for testing inheritance scenarios.
 *
 * Serves as a parent class in test stubs to verify property visibility and method accessibility in child classes.
 *
 * @copyright Copyright (C) 2025 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
abstract class TestBaseClass
{
    private string $propertyParent = 'valueParent';

    protected function inaccessibleParentMethod(): string
    {
        return $this->propertyParent;
    }
}
