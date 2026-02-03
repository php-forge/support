<?php

declare(strict_types=1);

namespace PHPForge\Support\Tests\Support\Provider;

use PHPForge\Support\Stub\{BackedInteger, BackedString, Unit};
use UnitEnum;

/**
 * Data provider for {@see \PHPForge\Support\Tests\EnumDataProviderTest} test cases.
 *
 * Provides representative input/output pairs for enum dataset generation.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class EnumDataProviderProvider
{
    /**
     * @return array<string, array{string, string|UnitEnum, bool, string, string, string}>
     */
    public static function casesParameters(): array
    {
        return [
            'enum backed integer instance' => [
                BackedInteger::class,
                BackedInteger::VALUE,
                true,
                'enum: 1',
                ' 1="1"',
                "Should return the '1' attribute value for enum case: 1.",
            ],
            'enum backed string instance' => [
                BackedString::class,
                'data-test',
                true,
                'enum: value',
                ' data-test="value"',
                "Should return the 'data-test' attribute value for enum case: value.",
            ],
            'enum unit instance' => [
                Unit::class,
                'data-test',
                false,
                'enum: value',
                ' data-test="value"',
                "Should return the 'data-test' attribute value for enum case: value.",
            ],
        ];
    }
}
