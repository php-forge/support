<?php

declare(strict_types=1);

namespace PHPForge\Support\Tests\Support\Provider;

use PHPForge\Support\Tests\Stub\TestEnum;

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
     * @return array<string, array{string, string|\UnitEnum, bool, string, string, string}>
     */
    public static function casesParameters(): array
    {
        return [
            'as enum instance' => [
                TestEnum::class,
                'data-test',
                false,
                'enum: FOO',
                ' data-test="FOO"',
                'Should return the enum instance for case: FOO.',
            ],
            'as html' => [
                TestEnum::class,
                'data-test',
                true,
                'enum: BAR',
                ' data-test="BAR"',
                "Should return the 'data-test' attribute value for enum case: BAR.",
            ],
            'attribute as enum instance' => [
                TestEnum::class,
                TestEnum::FOO,
                true,
                'enum: FOO',
                ' FOO="FOO"',
                "Should return the 'FOO' attribute value for enum case: FOO.",
            ],
        ];
    }
}
