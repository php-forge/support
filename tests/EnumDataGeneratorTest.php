<?php

declare(strict_types=1);

namespace PHPForge\Support\Tests;

use PHPForge\Support\EnumDataGenerator;
use PHPForge\Support\Tests\Support\Provider\EnumDataGeneratorProvider;
use PHPUnit\Framework\Attributes\{DataProviderExternal, Group};
use PHPUnit\Framework\TestCase;
use UIAwesome\Html\Helper\Enum;
use UnitEnum;

/**
 * Test suite for {@see EnumDataGenerator} utility methods.
 *
 * Verifies structured datasets generated from enum cases for both attribute fragment scenarios and tag scenarios.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
#[Group('support')]
final class EnumDataGeneratorTest extends TestCase
{
    /**
     * @phpstan-param class-string<UnitEnum> $enumClass
     */
    #[DataProviderExternal(EnumDataGeneratorProvider::class, 'casesParameters')]
    public function testCasesGenerateExpectedStructure(
        string $enumClass,
        string|UnitEnum $attribute,
        bool $asHtml,
    ): void {
        $data = match ($asHtml) {
            true => EnumDataGenerator::cases($enumClass, $attribute),
            false => EnumDataGenerator::cases($enumClass, $attribute, false),
        };

        self::assertNotEmpty($data, 'Should return at least one data set.');

        $attributeName = is_string($attribute) ? $attribute : sprintf('%s', Enum::normalizeValue($attribute));

        foreach ($enumClass::cases() as $case) {
            $expectedKey = 'enum: ' . Enum::normalizeValue($case);

            self::assertArrayHasKey(
                $expectedKey,
                $data,
                "Should include a dataset key for enum case '{$case->name}'.",
            );

            $row = $data[$expectedKey] ?? null;

            self::assertIsArray(
                $row,
                "Should return a dataset array for enum case '{$case->name}'.",
            );
            self::assertCount(
                4,
                $row,
                "Should return a 4-tuple dataset for enum case '{$case->name}'.",
            );

            /** @var array{UnitEnum, array<mixed>, string|UnitEnum, string} $row */
            [$value, $attributes, $expected, $message] = $row;

            self::assertSame(
                $case,
                $value,
                "Should store the enum case instance for '{$case->name}'.",
            );
            self::assertSame(
                [],
                $attributes,
                "Should store an empty attributes array for '{$case->name}'.",
            );

            $expectedValue = Enum::normalizeValue($case);

            if ($asHtml) {
                self::assertSame(
                    " {$attributeName}=\"{$expectedValue}\"",
                    $expected,
                    "Should generate expected attribute fragment for '{$case->name}'.",
                );
                $expectedMessage = "Should return the '{$attributeName}' attribute value for enum case: {$expectedValue}.";
            } else {
                self::assertSame(
                    $case,
                    $expected,
                    "Should return the enum instance as expected output for '{$case->name}'.",
                );
                $expectedMessage = "Should return the enum instance for case: {$expectedValue}.";
            }

            self::assertSame(
                $expectedMessage,
                $message,
                "Should store the expected message for '{$case->name}'.",
            );
        }
    }

    /**
     * @phpstan-param class-string<UnitEnum> $enumClass
     */
    #[DataProviderExternal(EnumDataGeneratorProvider::class, 'tagCasesParameters')]
    public function testTagCasesGenerateExpectedStructure(string $enumClass, string $category): void
    {
        $data = EnumDataGenerator::tagCases($enumClass, $category);

        self::assertNotEmpty($data, 'Should return at least one data set.');

        foreach ($enumClass::cases() as $case) {
            $expectedKey = Enum::normalizeValue($case) . " {$category} tag";

            self::assertArrayHasKey(
                $expectedKey,
                $data,
                "Should include a dataset key for enum case '{$case->name}'.",
            );

            $row = $data[$expectedKey] ?? null;

            self::assertIsArray(
                $row,
                "Should return a dataset array for enum case '{$case->name}'.",
            );
            self::assertCount(
                2,
                $row,
                "Should return a 2-tuple dataset for enum case '{$case->name}'.",
            );

            /** @var array{UnitEnum, string} $row */
            [$value, $normalizedValue] = $row;

            self::assertSame(
                $case,
                $value,
                "Should store the enum case instance for '{$case->name}'.",
            );
            self::assertSame(
                Enum::normalizeValue($case),
                $normalizedValue,
                "Should store normalized value for '{$case->name}'.",
            );
        }
    }
}
