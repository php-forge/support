<?php

declare(strict_types=1);

namespace PHPForge\Support;

use UIAwesome\Html\Helper\Enum;
use UnitEnum;

use function sprintf;

/**
 * Utility class for generating structured test data for enum-based attribute scenarios.
 *
 * Provides a standardized API for producing PHPUnit data sets built from `UnitEnum::cases()` and normalized values
 * produced by {@see Enum::normalizeValue()}.
 *
 * The generated data sets can be used to verify attribute fragments or enum instance handling in consumer tests.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class EnumDataGenerator
{
    /**
     * Generates test cases for enum-based attribute scenarios.
     *
     * Normalizes each enum case and produces an expected value as either an attribute fragment (when `$asHtml` is
     * `true`) or the enum case instance (when `$asHtml` is `false`).
     *
     * @phpstan-param class-string<UnitEnum> $enumClass Enum class name implementing UnitEnum.
     * @param string $enumClass Enum class name implementing UnitEnum.
     * @param string|UnitEnum $attribute Attribute name used to build the expected fragment.
     * @param bool $asHtml Whether to generate expected output as an attribute fragment or enum instance. Default is `true`.
     *
     * @return array Structured test cases indexed by a normalized enum value key.
     *
     * @phpstan-return array<string, array{UnitEnum, mixed[], string|UnitEnum, string}>
     */
    public static function cases(string $enumClass, string|UnitEnum $attribute, bool $asHtml = true): array
    {
        $cases = [];
        $attributeName = is_string($attribute) ? $attribute : sprintf('%s', Enum::normalizeValue($attribute));

        foreach ($enumClass::cases() as $case) {
            $normalizedValue = Enum::normalizeValue($case);

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
     * Produces a dataset mapping descriptive keys to enum cases and their normalized string values, suitable for
     * data provider methods in PHPUnit tests.
     *
     * @phpstan-param class-string<UnitEnum> $enumClass Enum class name implementing UnitEnum.
     * @param string $enumClass Enum class name implementing UnitEnum.
     * @param string $category Category label appended to the generated keys.
     *
     * @phpstan-return array<string, array{UnitEnum, string}> Structured test cases indexed by descriptive keys.
     */
    public static function tagCases(string $enumClass, string $category): array
    {
        $data = [];

        foreach ($enumClass::cases() as $case) {
            $value = (string) Enum::normalizeValue($case);
            $data[sprintf('%s %s tag', $value, $category)] = [$case, $value];
        }

        return $data;
    }
}
