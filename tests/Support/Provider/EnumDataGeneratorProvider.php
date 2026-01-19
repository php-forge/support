<?php

declare(strict_types=1);

namespace PHPForge\Support\Tests\Support\Provider;

use PHPForge\Support\Tests\Stub\TestEnum;

/**
 * Data provider for {@see \PHPForge\Support\Tests\EnumDataGeneratorTest} test cases.
 *
 * Provides representative input/output pairs for enum-based datasets.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class EnumDataGeneratorProvider
{
    /**
     * @return array<string, array{string, string|\UnitEnum, bool}>
     */
    public static function casesParameters(): array
    {
        return [
            'as html' => [
                TestEnum::class,
                'data-test',
                true,
            ],
            'as enum instance' => [
                TestEnum::class,
                'data-test',
                false,
            ],
            'attribute as enum instance' => [
                TestEnum::class,
                TestEnum::Foo,
                true,
            ],
        ];
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function tagCasesParameters(): array
    {
        return [
            'default category' => [
                TestEnum::class,
                'element',
            ],
        ];
    }
}
