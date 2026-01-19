<?php

declare(strict_types=1);

namespace PHPForge\Support;

use BackedEnum;
use UnitEnum;

use function sprintf;

/**
 * Utility class for generating structured test data from enum cases.
 *
 * Provides deterministic PHPUnit datasets built from `UnitEnum::cases()` and normalized enum values.
 *
 * Key features.
 * - Builds attribute fragment datasets via {@see EnumDataProvider::attributeCases()}.
 * - Builds tag datasets via {@see EnumDataProvider::tagCases()}.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class EnumDataProvider
{
    /**
     * Generates test cases for enum-based attribute scenarios.
     *
     * Normalizes each enum case and produces an expected value as either an attribute fragment (when `$asHtml` is
     * `true`) or the enum case instance (when `$asHtml` is `false`).
     *
     * @phpstan-param class-string<UnitEnum> $enumClass Enum class name implementing UnitEnum.
     *
     * @param string $enumClass Enum class name implementing UnitEnum.
     * @param string|UnitEnum $attribute Attribute name used to build the expected fragment.
     * @param bool $asHtml Whether to generate expected output as an attribute fragment or enum instance. Default is
     * `true`.
     *
     * @return array Structured test cases indexed by a normalized enum value key.
     *
     * @phpstan-return array<string, array{UnitEnum, mixed[], string|UnitEnum, string}>
     */
    public static function attributeCases(string $enumClass, string|UnitEnum $attribute, bool $asHtml = true): array
    {
        $cases = [];
        $attributeName = is_string($attribute) ? $attribute : sprintf('%s', self::normalizeValue($attribute));

        foreach ($enumClass::cases() as $case) {
            $normalizedValue = self::normalizeValue($case);

            $key = "enum: {$normalizedValue}";
            $expected = $asHtml ? " {$attributeName}=\"{$normalizedValue}\"" : $case;
            $message = $asHtml
                ? "Should return the '{$attributeName}' attribute value for enum case: {$normalizedValue}."
                : "Should return the enum instance for case: {$normalizedValue}.";

            $cases[$key] = [
                $case,
                [],
                $expected,
                $message,
            ];
        }

        return $cases;
    }

    /**
     * Generates test cases for tag-related enum scenarios.
     *
     * Produces a dataset mapping descriptive keys to enum cases and their normalized string values, suitable for data
     * provider methods in PHPUnit tests.
     *
     * @phpstan-param class-string<UnitEnum> $enumClass Enum class name implementing UnitEnum.
     *
     * @param string $enumClass Enum class name implementing UnitEnum.
     * @param string $category Category label appended to the generated keys.
     *
     * @phpstan-return array<string, array{UnitEnum, string}>
     */
    public static function tagCases(string $enumClass, string $category): array
    {
        $data = [];

        foreach ($enumClass::cases() as $case) {
            $value = self::normalizeValue($case);
            $data[sprintf('%s %s tag', $value, $category)] = [$case, $value];
        }

        return $data;
    }

    private static function normalizeValue(UnitEnum $enum): string
    {
        return match ($enum instanceof BackedEnum) {
            true => (string) $enum->value,
            false => $enum->name,
        };
    }
}
